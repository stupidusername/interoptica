<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "model".
 *
 * @property integer $id
 * @property string $gecom_code
 * @property string $name
 * @property integer $brand_id
 *
 * @property Brand $brand
 * @property Variant[] $variants
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id'], 'integer'],
            [['gecom_code', 'name'], 'string', 'max' => 255],
            [['gecom_code'], 'unique'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'id']],
			[['name', 'gecom_code'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gecom_code' => 'Código Gecom',
            'name' => 'Nombre',
            'brand_id' => 'ID Marca',
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
    public function getVariants()
    {
        return $this->hasMany(Variant::className(), ['model_id' => 'id']);
    }
}
