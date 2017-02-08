<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $variant_id
 * @property string $gecom_code
 * @property string $gecom_desc
 * @property string $price
 * @property integer $stock
 *
 * @property OrderProduct[] $orderProducts
 * @property Order[] $orders
 * @property Variant $variant
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variant_id', 'stock'], 'integer'],
            [['price'], 'number'],
            [['gecom_code', 'gecom_desc'], 'string', 'max' => 255],
            [['gecom_code', 'gecom_desc'], 'unique'],
            [['variant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Variant::className(), 'targetAttribute' => ['variant_id' => 'id']],
			[['gecom_code', 'gecom_desc', 'price'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'variant_id' => 'ID Variante',
            'gecom_code' => 'Código Gecom',
            'gecom_desc' => 'Descripción Gecom',
            'price' => 'Precio',
            'stock' => 'Stock',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['id' => 'order_id'])->viaTable('order_product', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariant()
    {
        return $this->hasOne(Variant::className(), ['id' => 'variant_id']);
    }
}
