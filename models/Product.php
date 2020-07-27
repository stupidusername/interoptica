<?php

namespace app\models;

use app\components\DeletedQuery;
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
 * @property string $barcode
 * @property boolean $polarized
 * @property boolean $mirrored
 * @property string $price
 * @property integer $stock
 * @property integer $stockAvailable
 * @property boolean $running_low
 * @property string $running_low_date
 * @property string $create_date
 * @property string $update_date
 * @property boolean $available
 * @property boolean $deleted
 *
 * @property OrderProduct[] $orderProducts
 * @property Order[] $orders
 * @property Variant $variant
 * @property Batch[] $batches
 */
class Product extends \yii\db\ActiveRecord
{
	const SCENARIO_UPDATE_STOCK = 'update_stock';

	const STOCK_MIN = 10;
	const STOCK_ALERT = 15;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'product';
	}

	public function attributes() {
		$attributes = parent::attributes();
		return array_merge($attributes, ['stock']);
	}

	public function getDirtyAttributes($names = null)
  {
      $attributes = parent::getDirtyAttributes($names);
			unset($attributes['stock']);
			return $attributes;
  }

	/** @inheritdoc */
	public function behaviors() {
		$behaviors = [
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
		];

		// Avoid adding gallery behavior in console apps.
		if (Yii::$app instanceof \yii\web\Application) {
			$behaviors['galleryBehavior'] = [
				'class' => GalleryBehavior::className(),
				'tableName' => 'product_image',
				'type' => 'product',
				'extension' => 'png',
				'directory' => Yii::getAlias('@webroot') . '/images/product',
				'url' => Yii::getAlias('@web') . '/images/product',
			];
		}

		return $behaviors;
	}

	/**
	* @inheritdoc
	*/
	public static function find() {
		$subquery = Batch::find()->select('SUM(stock)')->andWhere('product_id='. self::tableName() . '.id')->groupBy('product_id');
		$query = new DeletedQuery(get_called_class());
		$query->addSelect([self::tableName() . '.*', 'stock' => $subquery]);
		return $query;
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['model_id', 'polarized', 'mirrored', 'available'], 'integer'],
			[['barcode'], 'match', 'pattern' => '/^\d{13}$/', 'message' => 'El código de barras debe contener 13 numeros sin espacios ni guiones.'],
			[['price'], 'number'],
			[['stock'], 'integer', 'min' => 0, 'on' => self::SCENARIO_UPDATE_STOCK],
			[['stock'], 'integer'],
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
			'barcode' => 'Código de barras',
			'polarized' => 'Polarizado',
			'mirrored' => 'Espejado',
			'colors' => 'Colores',
			'colorNames' => 'Colores',
			'lensColors' => 'Colores lente',
			'lensColorNames' => 'Colores lente',
			'price' => 'Precio',
			'stock' => 'Stock',
			'stock_available' => 'Stock disponible',
			'running_low' => 'Agotándose',
			'running_low_date' => 'Agotándose desde',
			'create_date' => 'Fecha de alta',
			'update_date' => 'Fecha de modificación',
			'available' => 'Disponible',
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
			// Update stock
			if ($this->isAttributeChanged('stock', false)) {
				$oldStock = $this->getOldAttribute('stock');
				if ($oldStock > $this->stock) {
					$diff = $oldStock - $this->stock;
					$egress = new Egress();
					$egress->reason = Egress::REASON_STOCK_ADJUST;
					$egress->save();
					$egressProduct = new EgressProduct();
					$egressProduct->egress_id = $egress->id;
					$egressProduct->product_id = $this->id;
					$egressProduct->quantity = $diff;
					$egressProduct->save();
				} else if ($oldStock < $this->stock) {
					$diff = $this->stock - $oldStock;
					$batch = new Batch(['scenario' => Batch::SCENARIO_CREATE]);
					$batch->product_id = $this->id;
					$batch->entered_date = gmdate('Y-m-d');
					$batch->quantity = $diff;
					$batch->save();
				}
			}
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
	 * @return integer
	 */
	public function getStockAvailable() {
		return $this->stock - self::STOCK_MIN;
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
	public function getBatches()
	{
		return $this->hasMany(Batch::className(), ['product_id' => 'id']);
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
	* Checks if the stock is running low and sets the proper alert
	* @param $oldStock integer
	* @param $stock integer
	*/
	public function checkStock($oldStock, $stock) {
		$oldStock = $oldStock ?? 0;
		$stock = $stock ?? 0;
		if (!$this->running_low && $stock <= self::STOCK_ALERT && $oldStock > $stock) {
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
