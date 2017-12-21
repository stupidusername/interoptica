<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "model".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $brand_id
 * @property string $name
 * @property string $description
 * @property integer $front_size
 * @property integer $lens_width
 * @property integer $bridge_size
 * @property integer $temple_length
 * @property integer $base
 * @property boolean $flex
 * @property boolean $polarized
 * @property boolean $mirrored
 * @property boolean $deleted
 *
 * @property Brand $brand
 * @property ModelMaterial[] $modelMaterials
 * @property Product[] $products
 */
class Model extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'model';
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
            [['type', 'brand_id', 'front_size', 'lens_width', 'bridge_size', 'temple_length', 'base', 'flex', 'polarized', 'mirrored'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Tipo',
            'brand_id' => 'ID Marca',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'front_size' => 'Frente',
            'lens_width' => 'Calibre',
            'bridge_size' => 'Puente',
            'temple_length' => 'Patilla',
            'base' => 'Base',
            'flex' => 'Flex',
            'polarized' => 'Polarizado',
            'mirrored' => 'Espejado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelMaterials()
    {
        return $this->hasMany(ModelMaterial::className(), ['model_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['model_id' => 'id']);
    }
}
