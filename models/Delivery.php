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
 * @property string $transport
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
 *
 * @property string customerNames
 */
class Delivery extends \yii\db\ActiveRecord
{
	const SCENARIO_EDIT_STATUS = 'edit_status';
	const SCENARIO_EDIT_TRANSPORT = 'edit_transport';
	
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
			[['transport'], 'string', 'max' => 255],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['status'], 'required', 'on' => self::SCENARIO_EDIT_STATUS],
			[['transport'], 'required', 'when' => function() { return $this->status >= DeliveryStatus::STATUS_SENT; }],
			[['status'], 'validateStatus', 'on' => self::SCENARIO_EDIT_STATUS],
			[['transport'], 'validateTransport', 'on' => self::SCENARIO_EDIT_TRANSPORT],
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
			'transport' => 'Transporte',
			'customerNames' => 'Clientes',
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
	public function getDeliveryStatuses()
	{
		return $this->hasMany(DeliveryStatus::className(), ['delivery_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDeliveryStatus()
	{
		return $this->hasOne(DeliveryStatus::className(), ['delivery_id' => 'id'])->orderBy(['id' => SORT_DESC]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEnteredDeliveryStatus()
	{
		return $this->hasOne(DeliveryStatus::className(), ['delivery_id' => 'id'])->andWhere(['status' => DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT]);
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
		if($this->status >= DeliveryStatus::STATUS_SENT && !$this->transport) {
			$this->addError($attribute, 'Transporte no puede estar vacío.');
		}
	}

	public function validateTransport($attribute, $params, $validator) {
		if($this->status >= DeliveryStatus::STATUS_SENT) {
			$this->addError($attribute, 'No se puede editar un envio que no esta en estado ' . DeliveryStatus::statusLabels()[DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT]);
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
}
