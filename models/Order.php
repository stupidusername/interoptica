<?php

namespace app\models;

use app\helpers\MBStringHelper;
use kartik\mpdf\Pdf;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $customer_id
 * @property integer $transport_id
 * @property string $iva
 * @property string $discount_percentage
 * @property integer $order_condition_id
 * @property string $interest_rate_percentage
 * @property string $comment
 * @property integer $deleted
 * @property string $delete_datetime
 *
 * @property Customer $customer
 * @property Transport $transport
 * @property User $user
 * @property OrderProduct[] $orderProducts
 * @property Product[] $products
 * @property OrderStatus[] $orderStatuses
 * @property OrderStatus $orderStatus
 * @property OrderStatus $enteredOrderStatus
 * @property OrderInvoice[] $orderInvoices
 * @property OrderCondition $orderCondition
 * @property string $invoiceNumbers
 */
class Order extends \yii\db\ActiveRecord
{
	/**
	 * Scenarios
	 */
	const SCENARIO_VIEW = 'view';
	const SCENARIO_UPDATE = 'update';

	/**
	 * The last order status saved
	 * @var integer
	 */
	public $status;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'order';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'softDeleteBehavior' => [
				'class' => SoftDeleteBehavior::className(),
				'softDeleteAttributeValues' => [
					'deleted' => true,
					'delete_datetime' => gmdate('Y-m-d H:i:s'),
				],
				'replaceRegularDelete' => true
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function find()
	{
		return parent::find()->where(['or', [self::tableName() . '.deleted' => null], [self::tableName() . '.deleted' => 0]]);
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			if ($insert) {
				$this->user_id = Yii::$app->user->id;
				$this->iva = $this->customer->exclude_iva ? 0 : Yii::$app->params['iva'];
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		if ($insert) {
			$this->status = OrderStatus::STATUS_LOADING;
		}
		$oldStatus = $this->orderStatus;
		// Save order status if there was a change
		if (!$oldStatus || $oldStatus->status != $this->status) {
			$orderStatus = new OrderStatus();
			$orderStatus->order_id = $this->id;
			$orderStatus->user_id = Yii::$app->user->id;
			$orderStatus->status = $this->status;
			$orderStatus->create_datetime = gmdate('Y-m-d H:i:s');
			$orderStatus->save(false);
		}
		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->status = $this->orderStatus ? $this->orderStatus->status : null;
		parent::afterFind();
	}

	public function afterSoftDelete() {
		$this->restoreStock();
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = [
			[['customer_id', 'transport_id'], 'integer'],
			[['discount_percentage'], 'number', 'min' => 0, 'max' => 100],
			[['interest_rate_percentage'], 'number', 'min' => -100, 'max' => 100],
			[['comment'], 'string'],
			[['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
			[['transport_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transport::className(), 'targetAttribute' => ['transport_id' => 'id']],
			[['order_condition_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderCondition::className(), 'targetAttribute' => ['order_condition_id' => 'id']],
			[['customer_id', 'transport_id', 'order_condition_id', 'interest_rate_percentage'], 'required'],
			[['status'], 'required', 'on' => self::SCENARIO_UPDATE],
		];
		// Allow to edit user_id if user is admin
		if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) {
			$rules[] = [['user_id'], 'required', 'on' => self::SCENARIO_UPDATE];
			$rules[] = [['user_id'], 'integer', 'on' => self::SCENARIO_UPDATE];
			$rules[] = [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id'], 'on' => self::SCENARIO_UPDATE];
		}
		return $rules;
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_VIEW] = ['status'];
		return $scenarios;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_id' => 'ID Usuario',
			'customer_id' => 'ID Cliente',
			'transport_id' => 'ID Transporte',
			'iva' => 'IVA',
			'discount_percentage' => 'Porcentaje de Descuento',
			'order_condition_id' => 'ID Condición de Venta',
			'interest_rate_percentage' => 'Porcentaje de Interés',
			'comment' => 'Comentario',
			'status' => 'Estado',
			'invoiceNumbers' => 'Facturas',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustomer()
	{
		return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTransport() {
		return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrderProducts()
	{
		return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProducts()
	{
		return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('order_product', ['order_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrderStatuses()
	{
		return $this->hasMany(OrderStatus::className(), ['order_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrderStatus()
	{
		$subquery = OrderStatus::find()->select('MAX(id)')->groupBy('order_id');
		return $this->hasOne(OrderStatus::className(), ['order_id' => 'id'])->andWhere([OrderStatus::tableName() . '.id' => $subquery]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEnteredOrderStatus()
	{
		$subquery = OrderStatus::find()->select('MIN(id)')->andWhere(['status' => OrderStatus::STATUS_ENTERED])->groupBy('order_id');
		return $this->hasOne(OrderStatus::className(), ['order_id' => 'id'])->andWhere([OrderStatus::tableName() . '.status' => OrderStatus::STATUS_ENTERED, OrderStatus::tableName() . '.id' => $subquery]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrderInvoices()
	{
		return $this->hasMany(OrderInvoice::className(), ['order_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrderCondition()
	{
		return $this->hasOne(OrderCondition::className(), ['id' => 'order_condition_id']);
	}

	/**
	 * @return string
	 */
	public function getInvoiceNumbers() {
		return implode(', ', ArrayHelper::getColumn($this->orderInvoices, 'number'));
	}

	/**
	 * @return integer
	 */
	public function getTotalQuantity() {
		$quantity = 0;
		foreach ($this->orderProducts as $orderProduct) {
			if ($orderProduct->product->model->type != Model::TYPE_EXTRA) {
				$quantity += $orderProduct->quantity;
			}
		}
		return $quantity;
	}

	/**
	 * @return float
	 */
	public function getSubtotal() {
		$subtotal = 0;
		foreach ($this->orderProducts as $orderProduct) {
			$subtotal += $orderProduct->price * $orderProduct->quantity;
		}
		if ($this->discount_percentage) {
			$subtotal *= (1 - $this->discount_percentage / 100);
		}
		return $subtotal;
	}

	/**
	 * @return float
	 */
	public function getDiscount() {
		return $this->subtotal / (1 - $this->discount_percentage / 100);
	}

	/**
	 * @return float
	 */
	public function getSubtotalPlusIva() {
		return $this->subtotal * (1 + $this->iva / 100);
	}

	/**
	 * @return float
	 */
	public function getFinancing() {
		return $this->subtotalPlusIva * ($this->interest_rate_percentage / 100);
	}

	/**
	 * @return float
	 */
	public function getTotal() {
		return $this->subtotalPlusIva + $this->financing;
	}

	/**
	 * Content for the exported txt file
	 * @return string
	 */
	public function getTxt() {
		$output = '';
		$date = '';
		if ($this->enteredOrderStatus) {
			$date = Yii::$app->formatter->asDatetime($this->enteredOrderStatus->create_datetime, 'ddMMyy');
		}
		$customerGecomId = $this->customer->gecom_id;
		$userGecomId = $this->user->gecom_id;
		$orderProducts = $this->getOrderProducts()->with(['product'])->all();
		foreach ($orderProducts as $orderProduct) {
			$line = MBStringHelper::mb_str_pad($date, 13) .
				MBStringHelper::mb_str_pad($customerGecomId, 11) .
				MBStringHelper::mb_str_pad($orderProduct->quantity . $orderProduct->product->code, 19) .
				MBStringHelper::mb_str_pad(Yii::$app->formatter->asCurrency($orderProduct->price, ''), 9) .
				$userGecomId;
			$output .= $line . "\n";
		}
		return $output;
	}

	/**
	 * Name for the exported txt file
	 * @return string
	 */
	public function getTxtName() {
		return 'nota_pedido_' . $this->id . '.txt';
	}

	/**
	 * Restores the stock to product table in case of record deletion.
	 */
	private function restoreStock() {
		foreach ($this->orderProducts as $orderProduct) {
			$orderProduct->restoreStock();
		}
	}

	/**
	* Renders the PDF of an order.
	* @param integer $id id of the order
	* @param string $destination one of the destinations declared on Pdf class
	* @return mixed
	*/
	public static function getPdf($id, $destination) {
		$model = Order::find()->andWhere(['id' => $id])->with([
			'enteredOrderStatus',
			'user',
			'customer',
			'customer.zone',
			'orderProducts.order',
			'orderProducts.product.model',
		])->one();
		$header = Yii::$app->controller->renderPartial('pdf-header', ['model' => $model]);
		$content = Yii::$app->controller->renderPartial('pdf', ['model' => $model]);

		// setup kartik\mpdf\Pdf component
		$pdf = new Pdf([
			'filename' => $model->id . '.pdf',
			'mode' => Pdf::MODE_UTF8,
			'format' => Pdf::FORMAT_A4,
			'orientation' => Pdf::ORIENT_LANDSCAPE,
			'marginTop' => 0,
			'marginFooter' => 0,
			'destination' => $destination,
			'content' => $content,
			'cssFile' => '@webroot/css/pdf.css',
			'options' => [
				'setAutoTopMargin' => 'pad',
				'setAutoBottomMargin' => 'pad',
			],
			'methods' => [
				'SetHTMLHeader' => $header,
			],
		]);

		// return the pdf output as per the destination setting
		return $pdf->render();
	}
}
