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
 * @property integer $quantity
 * @property integer $ignore_stock
 *
 * @property Order $order
 * @property Product $product
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
			$oldProduct = Product::findOne($changedAttributes['product_id']);
			$oldProduct->updateStock($changedAttributes['quantity']);
		}
		$this->product->updateStock(-$this->quantity);
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
		$this->product->updateStock($this->quantity);
	}
}
