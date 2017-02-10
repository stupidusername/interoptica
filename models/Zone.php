<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "zone".
 *
 * @property integer $id
 * @property integer $gecom_id
 * @property string $name
 * @property boolean $deleted
 */
class Zone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zone';
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
            [['name'], 'string', 'max' => 255],
            [['gecom_id'], 'unique'],
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
            'name' => 'Nombre',
        ];
    }
	
	/**
	 * Gets an id => name array.
	 * return string[]
	 */
	public static function getIdNameArray() {
		$zones = ArrayHelper::map(self::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');
		return $zones;
	}
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['zone_id' => 'id']);
    }
}
