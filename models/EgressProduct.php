<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "egress_product".
 *
 * @property int $id
 * @property int $egress_id
 * @property int $product_id
 *
 * @property Egress $egress
 * @property Product $product
 * @property EgressProductBatch[] $egressProductBatches
 */
class EgressProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'egress_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['egress_id', 'product_id'], 'integer'],
            [['egress_id'], 'exist', 'skipOnError' => true, 'targetClass' => Egress::className(), 'targetAttribute' => ['egress_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'egress_id' => 'Egress ID',
            'product_id' => 'Product ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgress()
    {
        return $this->hasOne(Egress::className(), ['id' => 'egress_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgressProductBatches()
    {
        return $this->hasMany(EgressProductBatch::className(), ['egress_product_id' => 'id']);
    }
}
