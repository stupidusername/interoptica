<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_status".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $status
 * @property string $create_datetime
 *
 * @property Order $order
 */
class OrderStatus extends \yii\db\ActiveRecord
{
	const STATUS_ENTERED = 0;
	const STATUS_COLLECT = 1;
	const STATUS_PUT_TOGETHER = 2;
	const STATUS_BILLING = 3;
	const STATUS_PACKAGING = 4;
	const STATUS_SENT = 5;
	const STATUS_DELIVERED = 6;
	
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
			self::STATUS_COLLECT => 'Cobranzas',
			self::STATUS_PUT_TOGETHER => 'Armado',
			self::STATUS_BILLING => 'Facturación',
			self::STATUS_PACKAGING => 'Embalaje',
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
}
