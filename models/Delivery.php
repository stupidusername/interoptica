<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "delivery".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $transport_id
 * @property string $tracking_number
 * @property integer $deleted
 *
 * @property User $user
 * @property integer $type
 * @property string $typeLabel
 * @property DeliveryStatus[] $deliveryStatuses
 * @property DeliveryStatus $deliveryStatus
 * @property DeliveryStatus $enteredDeliveryStatus
 * @property Order[] $orders
 * @property Issue[] $issues
 * @property Transport $transport
 *
 * @property string customerNames
 * @property string invoiceNumbers
 */
class Delivery extends \yii\db\ActiveRecord
{
	const SCENARIO_EDIT_STATUS = 'edit_status';
	const SCENARIO_EDIT_TRANSPORT = 'edit_transport';
	const SCENARIO_EDIT_TRACKING_NUMBER = 'edit_tracking_number';

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
		return 'delivery';
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
	public static function find() {
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
			$this->status = DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT;
		}
		$oldStatus = $this->deliveryStatus;
		// Save delivery status if there was a change
		if ($this->status != DeliveryStatus::STATUS_ERROR) {
			if (!$oldStatus || $oldStatus->status != $this->status) {
				$deliveryStatus = new DeliveryStatus();
				$deliveryStatus->delivery_id = $this->id;
				$deliveryStatus->user_id = Yii::$app->user->id;
				$deliveryStatus->status = $this->status;
				$deliveryStatus->create_datetime = gmdate('Y-m-d H:i:s');
				$deliveryStatus->save(false);
			}
			$this->updateEntriesStatus();
		}
		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->status = $this->deliveryStatus ? $this->deliveryStatus->status : null;
		// Set error status if any entry differs from the delivery
		foreach ($this->orders as $order) {
			if ($this->status !== array_search($order->status, self::orderStatusMap())) {
				$this->status = DeliveryStatus::STATUS_ERROR;
			}
		}
		foreach ($this->issues as $issue) {
			if ($this->status !== array_search($issue->status, self::issueStatusMap())) {
				$this->status = DeliveryStatus::STATUS_ERROR;
			}
		}
		parent::afterFind();
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['tracking_number'], 'string', 'max' => 255],
			[['transport_id'], 'integer'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['status'], 'required', 'on' => self::SCENARIO_EDIT_STATUS],
			[['transport_id'], 'required', 'when' => function() { return $this->status >= DeliveryStatus::STATUS_SENT; }],
			[['transport_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transport::className(), 'targetAttribute' => ['transport_id' => 'id']],
			[['status'], 'validateStatus', 'on' => self::SCENARIO_EDIT_STATUS],
			[['transport'], 'validateTransport', 'on' => self::SCENARIO_EDIT_TRANSPORT],
			[['tracking_number'], 'validateTrackingNumber', 'on' => self::SCENARIO_EDIT_TRACKING_NUMBER],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_id' => 'ID Usuario',
			'status' => 'Estado',
			'transport_id' => 'ID Transporte',
			'tracking_number' => 'Número de Guía',
			'customerNames' => 'Clientes',
			'invoiceNumbers' => 'Facturas',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
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
	public function getDeliveryStatuses()
	{
		return $this->hasMany(DeliveryStatus::className(), ['delivery_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDeliveryStatus()
	{
		$subquery = DeliveryStatus::find()->select('MAX(id)')->groupBy('delivery_id');
		return $this->hasOne(DeliveryStatus::className(), ['delivery_id' => 'id'])->andWhere([DeliveryStatus::tableName() . '.id' => $subquery]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEnteredDeliveryStatus()
	{
		$subquery = DeliveryStatus::find()->select('MIN(id)')->groupBy('delivery_id');
		return $this->hasOne(DeliveryStatus::className(), ['delivery_id' => 'id'])->andWhere(['status' => DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT, DeliveryStatus::tableName() . '.id' => $subquery]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrders() {
		return $this->hasMany(Order::className(), ['id' => 'order_id'])->viaTable(DeliveryOrder::tableName(), ['delivery_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::className(), ['id' => 'issue_id'])->viaTable(DeliveryIssue::tableName(), ['delivery_id' => 'id']);
	}

	/**
	 * @return integer[]
	 */
	public static function orderStatusMap() {
		return [
			DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT => OrderStatus::STATUS_WAITING_FOR_TRANSPORT,
			DeliveryStatus::STATUS_SENT => OrderStatus::STATUS_SENT,
			DeliveryStatus::STATUS_DELIVERED => Orderstatus::STATUS_DELIVERED,
		];
	}

	/**
	 * @return integer[]
	 */
	public static function issueStatusMap() {
		return [
			DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT => IssueStatus::STATUS_WAITING_FOR_TRANSPORT,
			DeliveryStatus::STATUS_SENT => IssueStatus::STATUS_SENT,
			DeliveryStatus::STATUS_DELIVERED => IssueStatus::STATUS_DELIVERED,
		];
	}

	private function updateEntriesStatus() {
		foreach ($this->orders as $order) {
			$order->status = self::orderStatusMap()[$this->status];
			$order->save();
		}
		foreach ($this->issues as $issue) {
			$issue->status = self::issueStatusMap()[$this->status];
			$issue->save();
		}
	}

	public function validateStatus($attribute, $params, $validator) {
		if ($this->status >= DeliveryStatus::STATUS_SENT && !$this->transport) {
			$this->addError($attribute, 'Transporte no puede estar vacío.');
		}
	}

	public function validateTransport($attribute, $params, $validator) {
		if ($this->status >= DeliveryStatus::STATUS_SENT) {
			$this->addError($attribute, 'No se puede editar un envio que no esta en estado ' . DeliveryStatus::statusLabels()[DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT]);
		}
	}

	public function validateTrackingNumber($attribute, $params, $validator) {
		if ($this->status >= DeliveryStatus::STATUS_DELIVERED) {
			$this->addError($attribute, 'No se puede editar un envio que esta en estado ' . DeliveryStatus::statusLabels()[DeliveryStatus::STATUS_DELIVERED]);
		}
	}

	public function getCustomerNames() {
		$customers = [];
		foreach ($this->orders as $order) {
			$customer = $order->customer;
			$customers[$customer->id] = $customer;
		}
		foreach ($this->issues as $issue) {
			$customer = $issue->customer;
			$customers[$customer->id] = $customer;
		}
		return implode(', ', ArrayHelper::getColumn($customers, ['name']));
	}

	/**
	 * @return string
	 */
	public function getInvoiceNumbers() {
		$invoiceNumbersArray = [];
		foreach ($this->orders as $order) {
			if ($order->invoiceNumbers) $invoiceNumbersArray[] = $order->invoiceNumbers;
		}
		foreach ($this->issues as $issue) {
			if ($issue->invoiceNumbers) $invoiceNumbersArray[] = $issue->invoiceNumbers;
		}
		return implode(', ', $invoiceNumbersArray);
	}

	/**
	* This method sends an email for each order that belongs to this delivery.
	* The email must be sent when the tracking number of this delivery is entered.
	*/
	public function sendEmail() {
		foreach ($this->orders as $order) {
			if ($order->customer->email) {
				Yii::$app->mailer->compose('order/delivery-sent', ['model' => $order, 'delivery' => $this])
				->setFrom($order->user->email)
				->setTo($order->customer->email)
				->setSubject('Información de envio')
				->send();
			}
		}
	}
}
