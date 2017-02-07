<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "variant".
 *
 * @property integer $id
 * @property string $gecom_code
 * @property string $name
 * @property integer $model_id
 *
 * @property Product[] $products
 * @property Model $model
 */
class Variant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'variant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id'], 'integer'],
            [['gecom_code', 'name'], 'string', 'max' => 255],
            [['gecom_code'], 'unique'],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => Model::className(), 'targetAttribute' => ['model_id' => 'id']],
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
            'gecom_code' => 'Gecom Code',
            'name' => 'Name',
            'model_id' => 'Model ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['variant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(Model::className(), ['id' => 'model_id']);
    }
}
