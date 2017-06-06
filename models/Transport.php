<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "transport".
 *
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 *
 * @property Order[] $orders
 */
class Transport extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'transport';
	}

	/** 
	* @inheritdoc 
	*/
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

	/** 
	* @inheritdoc 
	*/
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
			[['deleted'], 'integer'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'required'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Nombre',
		];
	}

	/**
	 * Gets an id => name array.
	 * return string[]
	 */
	public static function getIdNameArray() {
		$transports = ArrayHelper::map(self::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
		return $transports;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrders()
	{
		return $this->hasMany(Order::className(), ['transport_id' => 'id']);
	}
}
