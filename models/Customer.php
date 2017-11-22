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
 * @property string $email
 * @property integer $zone_id
 * @property string $tax_situation
 * @property string $tax_situation_category
 * @property string $address
 * @property string $zip_code
 * @property string $province
 * @property string $locality
 * @property string $phone_number
 * @property string $doc_number
 * @property boolean $deleted
 */
class Customer extends \yii\db\ActiveRecord
{

	const SCENARIO_CREATE = 'create';

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
		return parent::find()->where(['or', [self::tableName() . '.deleted' => null], [self::tableName() . '.deleted' => 0]]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['gecom_id'], 'integer'],
			[['gecom_id', 'email'], 'unique'],
			[['email'], 'email'],
			[['name', 'tax_situation', 'tax_situation_category', 'address', 'zip_code', 'province', 'locality', 'phone_number', 'doc_number'], 'string', 'max' => 255],
			[['name'], 'required'],
			[['email', 'zone_id', 'tax_situation', 'address', 'zip_code', 'province', 'locality', 'phone_number', 'doc_number'], 'required', 'on' => self::SCENARIO_CREATE],
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
			'email' => 'Email',
			'zone_id' => 'ID Zona',
			'tax_situation' => 'Situación Impositiva',
			'tax_situation_category' => 'Categoría',
			'address' => 'Dirección',
			'zip_code' => 'Código Postal',
			'province' => 'Provincia',
			'locality' => 'Localidad',
			'phone_number' => 'Teléfonos',
			'doc_number' => 'Nro. de Documento',
		];
	}

	/**
	 * @return string[]
	 */
	public static function taxSituationLabels() {
		return [
			'O' => 'No categorizado',
			'I' => 'Inscripto',
			'M' => 'Monotributo',
			'E' => 'Exento',
		];
	}

	/**
	 * @return string
	 */
	public function getDisplayName() {
		return implode(' - ', [$this->gecom_id, $this->name, $this->locality]);
	}

	/**
	 * @return string
	 */
	public function getTaxSituationLabel() {
		return isset(self::taxSituationLabels()[$this->tax_situation]) ?
			self::taxSituationLabels()[$this->tax_situation] : $this->tax_situation;
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
