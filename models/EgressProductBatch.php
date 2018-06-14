<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "egress_product_batch".
 *
 * @property int $id
 * @property int $egress_product_id
 * @property int $batch_id
 * @property int $quantity
 *
 * @property Batch $batch
 * @property EgressProduct $egressProduct
 */
class EgressProductBatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'egress_product_batch';
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
    public function getEgressProduct()
    {
        return $this->hasOne(EgressProduct::className(), ['id' => 'egress_product_id']);
    }
}
