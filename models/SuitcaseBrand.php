<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "suitcase_brand".
 *
 * @property int $id
 * @property int $suitcase_id
 * @property int $brand_id
 *
 * @property Brand $brand
 * @property Suitcase $suitcase
 */
class SuitcaseBrand extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suitcase_brand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['suitcase_id', 'brand_id'], 'integer'],
            [['suitcase_id', 'brand_id'], 'required'],
            [['suitcase_id', 'brand_id'], 'unique', 'targetAttribute' => ['brand_id'], 'message' => 'Esta marca ya ha sido agregada a la valija.'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['suitcase_id'], 'exist', 'skipOnError' => true, 'targetClass' => Suitcase::className(), 'targetAttribute' => ['suitcase_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'suitcase_id' => 'ID Valija',
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
    public function getSuitcase()
    {
        return $this->hasOne(Suitcase::className(), ['id' => 'suitcase_id']);
    }
}
