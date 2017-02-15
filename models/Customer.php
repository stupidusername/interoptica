<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property integer $gecom_id
 * @property string $name
 * @property integer $zone_id
 * @property string $tax_situation
 * @property string $address
 * @property string $zip_code
 * @property string $locality
 * @property string $phone_number
 * @property string $doc_number
 * @property boolean $deleted
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
        return parent::find()->where(['or', ['deleted' => null], ['deleted' => 0]]);
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
			[['zone_id'], 'exist', 'targetClass' => Zone::className(), 'targetAttribute' => 'id'],
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
            'name' => 'Nombre',
			'zone_id' => 'ID Zona',
            'tax_situation' => 'Situación Impositiva',
            'address' => 'Dirección',
            'zip_code' => 'Código Postal',
            'locality' => 'Localidad',
            'phone_number' => 'Teléfonos',
            'doc_number' => 'Nro. de Documento',
        ];
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::className(), ['id' => 'zone_id']);
    }
	
	/**
	 * Gets an id => name array.
	 * return string[]
	 */
	public static function getIdNameArray() {
		$customers = ArrayHelper::map(self::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
		return $customers;
	}
}
