<?php

namespace app\models;

class ProductForm extends Product
{
	/**
	* @var string[]
	*/
	public $images = [];

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = parent::rules();
    $rules[] = [['images'], 'safe'];
    return $rules;
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
	public function afterSave($insert, $changedAttributes) {
		ProductImage::deleteAll(['product_id' => $this->id]);
    foreach ($this->images as $k => $image) {
      $productImage = new ProductImage();
      $productImage->product_id = $this->id;
      $productImage->filename = $image;
      $productImage->rank = $k;
      $productImage->save();
    }
		parent::afterSave($insert, $changedAttributes);
	}
}
