<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $variant_id
 * @property string $gecom_code
 * @property string $gecom_desc
 * @property string $price
 * @property integer $stock
 * @property boolean $deleted
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
	
	/** @inheritdoc */
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
	
	/** @inheritdoc */
	public static function find()
    {
        return (new ProductQuery(get_called_class()))->where(['or', ['deleted' => null], ['deleted' => 0]]);
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
	
	/**
	 * Gets an id => name array.
	 * return string[]
	 */
	public static function getIdNameArray() {
		$getName = function ($array, $defaultValue) {
			return $array['gecom_desc'] . ' (' . $array['stock'] . ')';
		};
		$products = ArrayHelper::map(self::find()->inStock()->select(['id', 'gecom_desc', 'stock'])->asArray()->all(), 'id', $getName);
		return $products;
	}
}

class ProductQuery extends yii\db\ActiveQuery
{
	public function inStock() {
		return $this->andWhere(['>', 'stock', 0]);
	}
}
