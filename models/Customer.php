<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property integer $gecom_id
 * @property string $name
 * @property string $tax_situation
 * @property string $address
 * @property string $zip_code
 * @property string $locality
 * @property string $phone_number
 * @property string $doc_number
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gecom_id'], 'integer'],
			[['gecom_id'], 'unique'],
            [['name', 'tax_situation', 'address', 'zip_code', 'locality', 'phone_number', 'doc_number'], 'string', 'max' => 255],
			[['gecom_id', 'name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gecom_id' => 'Gecom ID',
            'name' => 'Name',
            'tax_situation' => 'Tax Situation',
            'address' => 'Address',
            'zip_code' => 'Zip Code',
            'locality' => 'Locality',
            'phone_number' => 'Phone Number',
            'doc_number' => 'Doc Number',
        ];
    }
}
