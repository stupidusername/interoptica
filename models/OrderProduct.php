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
	public function afterSave($insert, $changedAttributes) {
		// Update product stock
		if ($insert) {
			$quantity = $this->quantity;
		} else {
			if (!isset($changedAttributes['quantity'])) {
				$quantity = 0;
			} else {
				$quantity = $this->quantity - $changedAttributes['quantity'];
			}
		}
		$this->product->updateCounters(['stock' => -$quantity]);
		parent::afterSave($insert, $changedAttributes);
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterDelete() {
		$this->product->updateCounters(['stock' => $this->quantity]);
		parent::afterDelete();
	}

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'price', 'quantity'], 'required'],
            [['product_id'], 'integer'],
			[['quantity'], 'integer', 'min' => 1],
            [['price'], 'number', 'min' => 0],
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
	 * Validates that entered quantity it's available
	 * @param string $attribute the attribute currently being validated
	 * @param mixed $params the value of the "params" given in the rule
	 * @param \yii\validators\InlineValidator related InlineValidator instance.
	 * This parameter is available since version 2.0.11.
	 */
	public function inStock($attribute, $param, $validator) {
		$stock = $this->product->stock !== null ? $this->product->stock : 0;
		if ($this->$attribute > $stock) {
			$this->addError($attribute, "El stock de este producto es de: $stock");
		}
	}

}
