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
 * @property boolean $extra
 * @property boolean $deleted
 *
 * @property OrderProduct[] $orderProducts
 * @property Order[] $orders
 * @property Variant $variant
 */
class Product extends \yii\db\ActiveRecord
{
	const STOCK_MIN = 10;

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
		return parent::find()->where(['or', [self::tableName() . '.deleted' => null], [self::tableName() . '.deleted' => 0]]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['variant_id', 'stock'], 'integer'],
			[['price'], 'number'],
			[['extra'], 'boolean'],
			[['gecom_code', 'gecom_desc'], 'string', 'max' => 255],
			[['gecom_code', 'gecom_desc'], 'unique'],
			[['variant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Variant::className(), 'targetAttribute' => ['variant_id' => 'id']],
			[['gecom_code', 'gecom_desc', 'price', 'extra'], 'required']
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
			'extra' => 'Extra',
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
	public function getOrderProducts()
	{
		return $this->hasMany(OrderProduct::className(), ['product_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssueProducts()
	{
		return $this->hasMany(IssueProduct::className(), ['product_id' => 'id']);
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
	 * @param OrderProduct $orderProduct if set, the quantity of $orderProduct
	 * will be added to its product stock.
	 * return string[]
	 */
	public static function getIdNameArray($orderProduct = null) {
		$getName = function ($array, $defaultValue) use ($orderProduct) {
			$stock = $array['stock'];
			if ($orderProduct && !$orderProduct->isNewRecord && $orderProduct->product_id == $array['id']) {
				$stock += $orderProduct->quantity;
			}
			return $array['gecom_desc'] . ' (' . $stock . ')';
		};
		$activeQuery = self::find()->inStock()->select(['id', 'gecom_desc', 'stock']);
		if ($orderProduct && !$orderProduct->isNewRecord) {
			$activeQuery->orWhere(['id' => $orderProduct->product_id]);
		}
		$products = ArrayHelper::map($activeQuery->asArray()->all(), 'id', $getName);
		return $products;
	}

	/**
	 * Gets products ordered by fail rate
	 * @param integere $limit
	 * @return mixed
	 */
	public static function getProductsOrderedByFailRate($limit = null) {
		$products = self::find()->select([
			self::tableName() . '.id',
			'gecom_desc',
			'fails' => IssueProduct::find()->select('sum(quantity)')->where('product_id = ' . Product::tableName() . '.id'),
			'orders' => OrderProduct::find()->select('sum(quantity)')->where('product_id = ' . Product::tableName() . '.id'),
		])->orderBy(['(fails / orders)' => SORT_DESC])->limit($limit)->asArray()->all();
		return array_filter($products, function ($product) {
			return $product['fails'] > 0 && $product['orders'] > 0;
		});
	}
}

class ProductQuery extends yii\db\ActiveQuery
{
	public function inStock() {
		return $this->andWhere(['>', 'stock', 0]);
	}
}
