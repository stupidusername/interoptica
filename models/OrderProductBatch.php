<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_product_batch".
 *
 * @property int $id
 * @property int $order_product_id
 * @property int $batch_id
 * @property int $quantity
 *
 * @property Batch $batch
 * @property OrderProduct $orderProduct
 */
class OrderProductBatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_product_batch';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(Batch::className(), ['id' => 'batch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProduct()
    {
        return $this->hasOne(OrderProduct::className(), ['id' => 'order_product_id']);
    }
}
