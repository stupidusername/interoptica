<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\validators\UniqueValidator;

/**
 * This is the model class for table "order_product".
 *
 * @property integer $order_id
 * @property integer $product_id
 * @property string $price
 * @property integer $ignore_stock
 *
 * @property Order $order
 * @property Product $product
 * @property integer $quantity
 */
class OrderProduct extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'order_product';
	}

	/**
	 * @inheritdoc
	 */
	public function attributes() {
		$attributes = parent::attributes();
		$attributes[] = 'quantity';
		return $attributes;
	}

	/**
	* @inheritdoc
	*/
	public static function find() {
		$subquery = OrderProductBatch::find()->select('SUM(quantity)')->andWhere('order_product_id='. self::tableName() . '.id')->groupBy('order_product_id');
		return parent::find()->addSelect([self::tableName() . '.*', 'quantity' => $subquery]);
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			if ($insert || $this->oldAttributes['product_id'] != $this->product_id) {
				$this->price = $this->product->price;
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		// Update product stock
		if (!$insert) {
			// Restore stock during update
			$this->restoreStock();
		}
		$this->updateStock();
		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @inheritdoc
	 */
	public function afterDelete() {
		$this->restoreStock();
		parent::afterDelete();
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['product_id', 'quantity'], 'required'],
			[['product_id'], 'integer'],
			[['quantity'], 'integer', 'min' => 1],
			[['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
			[['product_id'], 'uniqueEntry'],
			[['quantity'], 'inStock', 'when' => function ($model) { return !$this->ignore_stock; }],
			[['ignore_stock'], 'boolean'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'order_id' => 'ID Pedido',
			'product_id' => 'ID Producto',
			'price' => 'Precio',
			'quantity' => 'Cantidad',
			'ignore_stock' => 'Ignorar límite de stock',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrder()
	{
		return $this->hasOne(Order::className(), ['id' => 'order_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProduct()
	{
		return $this->hasOne(Product::className(), ['id' => 'product_id'])->where([]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrderProductBatches()
	{
		return $this->hasMany(OrderProductBatch::className(), ['order_prodcut_id' => 'id']);
	}

	/**
	 * @return float
	 */
	public function getSubtotal() {
		return $this->price * $this->quantity;
	}

	/**
	 * Validates that entered quantity it's available
	 * @param string $attribute the attribute currently being validated
	 * @param mixed $params the value of the "params" given in the rule
	 * @param \yii\validators\InlineValidator related InlineValidator instance.
	 * This parameter is available since version 2.0.11.
	 */
	public function inStock($attribute, $param, $validator) {
		$oldQuantity = isset($this->oldAttributes['quantity']) ? $this->oldAttributes['quantity'] : 0;
		$stock = $this->product->stock !== null ? $this->product->stock : 0;
		$realStock = $oldQuantity + $stock;
		if ($this->$attribute > $realStock - Product::STOCK_MIN) {
			$this->addError($attribute, "El stock de este producto es de $realStock y tienen que quedar " . Product::STOCK_MIN. ".");
		}
	}

	/**
	 * Validates that entered entry it's unique
	 * @param string $attribute the attribute currently being validated
	 * @param mixed $params the value of the "params" given in the rule
	 * @param \yii\validators\InlineValidator related InlineValidator instance.
	 * This parameter is available since version 2.0.11.
	 */
	public function uniqueEntry($attribute, $param, $validator) {
		$validator = new UniqueValidator([
			'targetAttribute' => ['order_id', 'product_id'],
		]);
		$validator->validateAttribute($this, $attribute);
		if ($this->hasErrors($attribute)) {
			$this->clearErrors($attribute);
			$this->addError($attribute, 'El producto ya se encuentra en el pedido. ' .
				Html::a('Editar', ['/order/update-entry', 'orderId' => $this->order_id, 'productId' => $this->product_id], ['class' => 'productUpdate']) . '.');
		}
	}

	/**
	 * Restores stock to products table in case of record deletion.
	 */
	public function restoreStock() {
		$orderProductBathches = $this->getOrderProductBatches()->with(['batch'])->all();
		foreach ($orderProductBatches as $orderProductBatch) {
			$orderProductBatch->batch->updatestock($order->productBatch->quantity);
			$orderProductBatch->delete();
		}
	}

	/**
	* Update product stock
	* @param integer $productId
	* @param integer $quantity
	*/
	private function updateStock() {
		$quantity = $this->quantity;
		$batches = Batch::find()->andWhere(['product_id' => $this->product_id])->andWhere(['>', 'stock', 0])->orderBy(['id' => SORT_ASC])->all();
		foreach ($batches as $batch) {
			if ($quantity <= $batch->stock) {
				$removedQuantity = $quantity;
			} else {
				$removedQuantity = $batch->stock;
			}
			$batch->updateStock(-$removedQuantity);
			$orderProductBatch = new OrderProductBatch();
			$orderProductBatch->order_product_id = $this->id;
			$orderProductBatch->batch_id = $batch->id;
			$orderProductBatch->save(false);
			$quantity -= $removedQuantity;
			if ($quantity <= 0) {
				break;
			}
		}
		if ($quantity) {
			$batch = Batch::find()->andWhere(['product_id' => $this->product_id])->orderBy(['id' => SORT_DESC])->all();
			if ($batch) {
				$batch->updateStock(-$quantity);
			} else {
				$batch = new Batch();
				$batch->product_id = $this->product_id;
				$batch->entered_date = gmdate('Y-m-d');
				$batch->quantity = 0;
				$batch->stock = -$quantity;
				$batch->save(false);
			}
			$orderProductBatch = new OrderProductBatch();
			$orderProductBatch->order_product_id = $this->id;
			$orderProductBatch->batch_id = $batch->id;
			$orderProductBatch->save(false);
		}
	}
}
