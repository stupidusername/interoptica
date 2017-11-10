<?php

namespace app\models;

use app\helpers\MBStringHelper;
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
 * @property string $discount_percentage
 * @property string $comment
 * @property integer $deleted
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
					'deleted' => true
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
			$this->status = OrderStatus::STATUS_ENTERED;
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
			[['comment'], 'string'],
			[['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
			[['transport_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transport::className(), 'targetAttribute' => ['transport_id' => 'id']],
			[['customer_id', 'transport_id'], 'required'],
			[['status'], 'required', 'on' => self::SCENARIO_UPDATE],
			[['status'], 'requireInvoice'],
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
			'discount_percentage' => 'Porcentaje de Descuento',
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
		$subquery = OrderStatus::find()->select('MIN(id)')->groupBy('order_id');
		return $this->hasOne(OrderStatus::className(), ['order_id' => 'id'])->andWhere(['status' => OrderStatus::STATUS_ENTERED, OrderStatus::tableName() . '.id' => $subquery]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrderInvoices()
	{
		return $this->hasMany(OrderInvoice::className(), ['order_id' => 'id']);
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
			if (!$orderProduct->product->extra) {
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
		return $subtotal;
	}

	/**
	 * @return float
	 */
	public function getDiscountedFromSubtotal() {
		if ($this->discount_percentage) {
			$value = $this->subtotal * ($this->discount_percentage / 100);
		} else {
			$value = 0;
		}
		return $value;
	}

	/**
	 * @return float
	 */
	public function getTotal() {
		return ($this->subtotal - $this->discountedFromSubtotal) * (1 + (float) Yii::$app->params['iva'] / 100);
	}

	/**
	 * Content for the exported txt file
	 * @return string
	 */
	public function getTxt() {
		$output = '';
		$date = Yii::$app->formatter->asDatetime($this->enteredOrderStatus->create_datetime, 'ddMMyy');
		$customerGecomId = $this->customer->gecom_id;
		$userGecomId = $this->user->gecom_id;
		$orderProducts = $this->getOrderProducts()->with(['product'])->all();
		foreach ($orderProducts as $orderProduct) {
			$line = MBStringHelper::mb_str_pad($date, 13) .
				MBStringHelper::mb_str_pad($customerGecomId, 11) .
				MBStringHelper::mb_str_pad($orderProduct->quantity . $orderProduct->product->gecom_code, 19) .
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
	 * Require order invoices for packaging
	 * @param string $attribute the attribute currently being validated
	 * @param mixed $params the value of the "params" given in the rule
	 * @param \yii\validators\InlineValidator related InlineValidator instance.
	 */
	public function requireInvoice($attribute, $params, $validator) {
		if ($this->status > OrderStatus::STATUS_BILLING && !$this->getOrderInvoices()->count()) {
			$this->addError($attribute, 'No se encuentran facturas registradas a este pedido.');
		}
	}
}
