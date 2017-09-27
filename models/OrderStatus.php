<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_status".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $user_id
 * @property integer $status
 * @property string $create_datetime
 *
 * @property Order $order
 */
class OrderStatus extends \yii\db\ActiveRecord
{
	const STATUS_ENTERED = 0;
	const STATUS_COLLECT = 1;
	const STATUS_COLLECT_REVISION = 2;
	const STATUS_ADMINISTRATION = 3;
	const STATUS_PENDING_PUT_TOGETHER = 4;
	const STATUS_PUT_TOGETHER = 5;
	const STATUS_PUT_TOGETHER_PRINTED = 6;
	const STATUS_BILLING = 7;
	const STATUS_PACKAGING = 8;
	const STATUS_WAITING_FOR_TRANSPORT = 9;
	const STATUS_SENT = 10;
	const STATUS_DELIVERED = 11;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'order_status';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['order_id', 'status'], 'integer'],
			[['create_datetime'], 'safe'],
			[['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'order_id' => 'ID Pedido',
			'user_id' => 'ID Usuario',
			'status' => 'Estado',
			'create_datetime' => 'Fecha',
		];
	}

	/**
	 * Status labels
	 * @return string[]
	 */
	public static function statusLabels() {
		return [
			self::STATUS_ENTERED => 'Ingresado',
			self::STATUS_COLLECT => 'Crédito',
			self::STATUS_COLLECT_REVISION => 'Revisión crédito',
			self::STATUS_ADMINISTRATION => 'Administración',
			self::STATUS_PENDING_PUT_TOGETHER => 'Armado pendiente',
			self::STATUS_PUT_TOGETHER => 'Armado',
			self::STATUS_PUT_TOGETHER_PRINTED => 'Armado impreso',
			self::STATUS_BILLING => 'Facturación',
			self::STATUS_PACKAGING => 'Embalaje',
			self::STATUS_WAITING_FOR_TRANSPORT => 'Esperando transporte',
			self::STATUS_SENT => 'Enviado',
			self::STATUS_DELIVERED => 'Entregado',
		];
	}

	/**
	 * @return string
	 */
	public function getStatusLabel() {
		return self::statusLabels()[$this->status];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrder()
	{
		return $this->hasOne(Order::className(), ['id' => 'order_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * Get last status of each order
	 * @return \yii\db\ActiveQuery
	 */
	public static function getLastStatuses() {
		return self::find()->select(['id' => 'max(id)'])->asArray()->groupBy('order_id');
	}
}
