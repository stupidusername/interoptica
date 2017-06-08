<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery_status".
 *
 * @property integer $id
 * @property integer $delivery_id
 * @property integer $status
 * @property string $create_datetime
 *
 * @property string $statusLabel
 * @property Delivery $delivery
 */
class DeliveryStatus extends \yii\db\ActiveRecord
{
	const STATUS_ERROR = -1;
	const STATUS_WAITING_FOR_TRANSPORT = 0;
	const STATUS_SENT = 1;
	const STATUS_DELIVERED = 2;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'delivery_status';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['delivery_id', 'status'], 'integer'],
			[['create_datetime'], 'safe'],
			[['delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Delivery::className(), 'targetAttribute' => ['delivery_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'delivery_id' => 'ID Entrega',
			'status' => 'Estado',
			'create_datetime' => 'Fecha',
		];
	}

	/**
	 * @return string[]
	 */
	public static function statusLabels() {
		return [
			self::TYPE_ERROR => 'Error',
			self::TYPE_WAITING_FOR_TRASNPORT => 'Esperando transporte',
			self::TYPE_SENT => 'Enviado',
			self::TYPE_DELIVERED => 'Entregado',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDelivery()
	{
		return $this->hasOne(Delivery::className(), ['id' => 'delivery_id']);
	}

	/**
	 * @return string
	 */
	public function getStatusLabel() {
		return self::statusLabels()[$this->status];
	}
}
