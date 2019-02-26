<?php

namespace app\models;

use yii\base\Model;

class OrderProductsForm extends Model {

  /**
  * @var integer
  */
  public $orderId;

  /**
  * @var [integer]
  */
  public $productIds = [];

  /**
  * @var integer
  */
  public $quantity;

  /**
  * @var boolean
  */
  public $ignoreStock;

  /**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['productIds', 'quantity'], 'required'],
			[['quantity'], 'integer', 'min' => 1],
			[['ignoreStock'], 'boolean'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'quantity' => 'Cantidad',
			'ignore_stock' => 'Ignorar límite de stock',
		];
	}

  /**
  * Save all product entries
  * @return boolean
  */
  public function save() {
    if ($this->validate()) {
      $models = [];
      $error = '';
      foreach ($this->productIds as $productId) {
        $model = new OrderProduct();
        $model->order_id = $this->orderId;
        $model->product_id = $productId;
        $model->quantity = (int) $this->quantity;
        $model->ignore_stock = $this->ignoreStock;
        if (!$model->validate()) {
          if ($error) {
            $error .= '<br>';
          }
          $error .= $model->product->code . ': ';
          foreach ($model->getErrors() as $modelErrors) {
            foreach ($modelErrors as $errorMsg) {
              $error .= $errorMsg;
            }
          }
        }
        $models[] = $model;
      }
      if ($error) {
        $this->addError('productIds', $error);
        return false;
      } else {
        foreach ($models as $model) {
          $model->save(false);
        }
        return true;
      }
    } else {
      return false;
    }
  }

}
