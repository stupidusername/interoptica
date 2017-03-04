<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_product".
 *
 * @property integer $order_id
 * @property integer $product_id
 * @property string $price
 * @property integer $quantity
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
			$oldProduct->updateCounters(['stock' => $changedAttributes['quantity']]);
		}
		$this->product->updateCounters(['stock' => -$this->quantity]);
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
			[['product_id'], 'unique', 'targetAttribute' => ['order_id', 'product_id'], 'message' => 'El producto ya se encuentra en el pedido.'],
			[['quantity'], 'inStock'],
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
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
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
		if ($this->$attribute > $realStock) {
			$this->addError($attribute, "El stock de este producto es de: $realStock");
		}
	}

	/**
	 * Restores stock to products table in case of record deletion.
	 */
	public function restoreStock() {
		$this->product->updateCounters(['stock' => $this->quantity]);
	}
}
