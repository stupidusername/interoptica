<?php

namespace app\models;

use Yii;
use yii\validators\UniqueValidator;

/**
 * This is the model class for table "collection_product".
 *
 * @property int $id
 * @property int $collection_id
 * @property int $product_id
 *
 * @property Collection $collection
 * @property Product $product
 */
class CollectionProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'collection_product';
    }

    /**
  	 * @inheritdoc
  	 */
  	public function rules()
  	{
  		return [
  			[['product_id'], 'required'],
  			[['product_id'], 'integer'],
  			[['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
  			[['product_id'], 'uniqueEntry'],
  		];
  	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'collection_id' => 'ID Colección',
            'product_id' => 'ID Producto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCollection()
    {
        return $this->hasOne(Collection::className(), ['id' => 'collection_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
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
  			'targetAttribute' => ['collection_id', 'product_id'],
  		]);
  		$validator->validateAttribute($this, $attribute);
  		if ($this->hasErrors($attribute)) {
  			$this->clearErrors($attribute);
  			$this->addError($attribute, 'El producto ya se encuentra en la colección.');
  		}
  	}
}
