<?php

namespace app\models;

use app\jobs\UpdateCustomerIVAJob;
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
 * @property string $discount_percentage
 * @property integer $zone_id
 * @property string $tax_situation
 * @property string $tax_situation_category
 * @property boolean $exclude_iva
 * @property string $address
 * @property string $zip_code
 * @property string $province
 * @property string $locality
 * @property string $phone_number
 * @property string $cuit
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
			[['gecom_id'], 'unique'],
			[['email'], 'email'],
			[['discount_percentage'], 'number', 'min' => 0, 'max' => 100],
			[['name', 'tax_situation', 'tax_situation_category', 'address', 'zip_code', 'province', 'locality', 'phone_number', 'cuit'], 'string', 'max' => 255],
			['cuit', 'match', 'pattern' => '/^\d{2}-\d{8}-\d$/', 'message' => 'El número de CUIT debe tener el formato: XX-XXXXXXXX-X'],
			[['name'], 'required'],
			[['gecom_id', 'email', 'zone_id', 'tax_situation', 'address', 'zip_code', 'province', 'locality', 'phone_number', 'cuit'], 'required', 'on' => self::SCENARIO_CREATE],
			[['zone_id'], 'exist', 'targetClass' => Zone::className(), 'targetAttribute' => 'id'],
			[['exclude_iva'], 'boolean'],
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
			'discount_percentage' => 'Descuento Asignado',
			'zone_id' => 'ID Zona',
			'tax_situation' => 'Situación Impositiva',
			'tax_situation_category' => 'Categoría',
			'exclude_iva' => 'Omitir IVA',
			'address' => 'Dirección',
			'zip_code' => 'Código Postal',
			'province' => 'Provincia',
			'locality' => 'Localidad',
			'phone_number' => 'Teléfonos',
			'cuit' => 'CUIT',
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
	 * @return string[]
	 */
	public static function getIdNameArray() {
		$customers = ArrayHelper::map(self::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
		return $customers;
	}
}
