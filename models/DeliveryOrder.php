<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery_order".
 *
 * @property integer $id
 * @property integer $delivery_id
 * @property integer $order_id
 *
 * @property Delivery $delivery
 * @property Order $order
 */
class DeliveryOrder extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'delivery_order';
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		if ($insert) {
			$this->order->status = Delivery::orderStatusMap()[$this->delivery->status];
			$this->order->save();
		}
		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['delivery_id', 'order_id'], 'integer'],
			[['delivery_id', 'order_id'], 'required'],
			[['delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Delivery::className(), 'targetAttribute' => ['delivery_id' => 'id']],
			[['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
			[
				['order_id'],
				'unique',
				'when' => function() { return count(self::find()->andWhere(['order_id' => $this->order_id])->innerJoinWith('delivery')); },
				'message' => 'Este pedido ya se encuentra asociado a un envio.',
			],
			[['order_id'], 'unique', 'targetAttribute' => ['delivery_id', 'order_id'], 'message' => 'El pedido ya se encuentra en el envio.'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'delivery_id' => 'ID Envio',
			'order_id' => 'ID Pedido',
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
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrder()
	{
		return $this->hasOne(Order::className(), ['id' => 'order_id']);
	}
}
