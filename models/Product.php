<?php

namespace app\models;

use dosamigos\taggable\Taggable;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\ArrayHelper;
use zxbodya\yii2\galleryManager\GalleryBehavior;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $model_id
 * @property string $code
 * @property string $price
 * @property integer $stock
 * @property boolean $running_low
 * @property string $running_low_date
 * @property string $create_date
 * @property string $update_date
 * @property boolean $deleted
 *
 * @property OrderProduct[] $orderProducts
 * @property Order[] $orders
 * @property Variant $variant
 */
class Product extends \yii\db\ActiveRecord
{
	const STOCK_MIN = 10;
	const STOCK_ALERT = 15;

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
			'colors' => [
				'class' => Taggable::className(),
				'attribute' => 'colorNames',
				'relation' => 'colors',
			],
			'lensColors' => [
				'class' => Taggable::className(),
				'attribute' => 'lensColorNames',
				'relation' => 'lensColors',
			],
			'galleryBehavior' => [
				'class' => GalleryBehavior::className(),
				'tableName' => 'product_image',
				'type' => 'product',
				'extension' => 'png',
				'directory' => Yii::getAlias('@webroot') . '/images/product',
				'url' => Yii::getAlias('@web') . '/images/product',
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
			[['model_id', 'stock'], 'integer'],
			[['price'], 'number'],
			[['code'], 'string', 'max' => 255],
			[['code'], 'unique'],
			[['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => Model::className(), 'targetAttribute' => ['model_id' => 'id']],
			[['model_id', 'code', 'price'], 'required'],
			[['colorNames', 'lensColorNames'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'model_id' => 'ID Modelo',
			'code' => 'Código',
			'colors' => 'Colores',
			'colorNames' => 'Colores',
			'lensColors' => 'Colores lente',
			'lensColorNames' => 'Colores lente',
			'price' => 'Precio',
			'stock' => 'Stock',
			'running_low' => 'Agotándose',
			'running_low_date' => 'Agotándose desde',
			'create_date' => 'Fecha de alta',
			'update_date' => 'Fecha de modificación',
		];
	}

	/**
	* @inheritdoc
	*/
	public function beforeSave($insert) {
		if (!parent::beforeSave($insert)) {
			return false;
		}
		if ($insert) {
			$this->create_date = gmdate('Y-m-d');
		} else {
			// Changing the stock should not affect update_date
			$attrs = $this->attributes;
			unset($attrs['stock'], $attrs['running_low'], $attrs['running_low_date']);
			foreach (array_keys($attrs) as $attrName) {
				if ($this->isAttributeChanged($attrName, false)) {
					$this->update_date = gmdate('Y-m-d');
					break;
				}
			}
		}
		return true;
	}

	/**
	* @inheritdoc
	*/
	public function afterSave($insert, $changedAttributes) {
		if (!$insert && array_key_exists('stock', $changedAttributes)) {
			$this->checkStock($changedAttributes['stock'], $this->stock);
		}
		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getModel()
	{
			return $this->hasOne(Model::className(), ['id' => 'model_id']);
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
	public function getColors()
	{
			return $this->hasMany(Color::className(), ['id' => 'color_id'])->viaTable('product_color', ['product_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLensColors()
	{
			return $this->hasMany(Color::className(), ['id' => 'color_id'])->viaTable('product_lens_color', ['product_id' => 'id']);
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
			return $array['code'] . ' (' . $stock . ')';
		};
		$activeQuery = self::find()->inStock()->select(['id', 'code', 'stock']);
		if ($orderProduct && !$orderProduct->isNewRecord) {
			$activeQuery->orWhere(['id' => $orderProduct->product_id]);
		}
		$products = ArrayHelper::map($activeQuery->asArray()->all(), 'id', $getName);
		return $products;
	}

	/**
	 * Gets products ordered by fail rate
	 * @param integer $limit
	 * @return mixed
	 */
	public static function getProductsOrderedByFailRate($limit = null) {
		$products = self::find()->select([
			self::tableName() . '.id',
			'code',
			'fails' => IssueProduct::find()->select('sum(quantity)')->where('product_id = ' . Product::tableName() . '.id'),
			'orders' => OrderProduct::find()->select('sum(quantity)')->where('product_id = ' . Product::tableName() . '.id'),
		])->orderBy(['(fails / orders)' => SORT_DESC])->limit($limit)->asArray()->all();
		return array_filter($products, function ($product) {
			return $product['fails'] > 0 && $product['orders'] > 0;
		});
	}

	/**
	 * Updates product stock
	 */
	public function updateStock($quantity) {
		if (is_null($this->stock)) {
			$this->stock = 0;
			$this->save(false);
		}
		$this->checkStock($this->stock, $this->stock + $quantity);
		$this->updateCounters(['stock' => $quantity]);
	}

	/**
	* Checks if the stock is running low and sets the proper alert
	* @param $oldStock integer
	* @param $stock integer
	*/
	public function checkStock($oldStock, $stock) {
		$oldStock = $oldStock ?? 0;
		$stock = $stock ?? 0;
		if ($stock <= self::STOCK_ALERT && $oldStock > $stock) {
			$this->running_low = true;
			$this->running_low_date = gmdate('Y-m-d');
			$this->save(false);
		} elseif ($stock > self::STOCK_ALERT) {
			$this->running_low = 0;
			$this->running_low_date = null;
			$this->save(false);
		}
	}
}

class ProductQuery extends yii\db\ActiveQuery
{
	public function inStock() {
		return $this->andWhere(['>', 'stock', 0]);
	}
}
