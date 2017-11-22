<?php

namespace app\models;

use app\validators\InvoiceValidator;
use Yii;

/**
 * This is the model class for table "order_invoice".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $number
 * @property string $comment
 *
 * @property Order $order
 */
class OrderInvoice extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'order_invoice';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['order_id', 'number'], 'required'],
			[['order_id'], 'integer'],
			[['number'], 'unique'],
			[['number'], InvoiceValidator::className()],
			[['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
			[['comment'], 'string'],
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
			'number' => 'Número de Factura',
			'comment' => 'Comentario',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrder()
	{
		return $this->hasOne(Order::className(), ['id' => 'order_id']);
	}
}
