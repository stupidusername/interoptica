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
    // Model types
    const TYPE_SUN = 0;
    const TYPE_RX = 1;
    const TYPE_CASE_SUN = 2;
    const TYPE_CASE_RX = 3;
    const TYPE_DISPLAY_SUN = 4;
    const TYPE_DISPLAY_RX = 5;
    const TYPE_EXTRA = 6;

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
  					'deleted' => true,
  				],
  				'replaceRegularDelete' => true,
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
            [['type', 'brand_id', 'name'], 'required'],
            [['front_size', 'lens_width', 'bridge_size', 'temple_length', 'base', 'flex'], 'required', 'when' => function ($model) { return in_array($this->type, [self::TYPE_SUN, self::TYPE_RX]); }],
            [['polarized', 'mirrored'], 'required', 'when' => function ($model) { return $this->type === self::TYPE_SUN; }],
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
    * @return string[]
    */
    public static function typeLabels() {
      return [
        static::TYPE_SUN => 'Sol',
        static::TYPE_RX => 'Receta',
        static::TYPE_CASE_SUN => 'Estuche Sol',
        static::TYPE_CASE_RX => 'Estuche Receta',
        static::TYPE_DISPLAY_SUN => 'Exhibidor Sol',
        static::TYPE_DISPLAY_RX => 'Exhibidor Receta',
        static::TYPE_EXTRA => 'Extra',
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

    /**
    * @return string
    */
    public function getTypeLabel() {
      return self::typeLabels()[$this->type];
    }
}
