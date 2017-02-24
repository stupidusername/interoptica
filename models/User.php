<?php

namespace app\models;

use dektrium\user\models\User as BaseUser;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\ArrayHelper;

/**
 * @property integer $gecom_id
 */
class User extends BaseUser {

	/** @inheritdoc */
	public function behaviors() {
		$behaviors = parent::behaviors();
		$behaviors['softDeleteBehavior'] = [
			'class' => SoftDeleteBehavior::className(),
			'softDeleteAttributeValues' => [
				'deleted' => true
			],
			'replaceRegularDelete' => true
		];
		return $behaviors;
	}
	
	/** @inheritdoc */
	public static function find()
    {
        return parent::find()->where(['or', ['deleted' => null], ['deleted' => 0]]);
    }

	/** @inheritdoc */
	public function scenarios() {
		$scenarios = parent::scenarios();
		// add field to scenarios
		$scenarios['create'][] = 'gecom_id';
		$scenarios['update'][] = 'gecom_id';
		$scenarios['register'][] = 'gecom_ud';
		return $scenarios;
	}

	/** @inheritdoc */
	public function rules() {
		$rules = parent::rules();
		// add some rules
		$rules['gecomIDType'] = ['gecom_id', 'integer'];
		$rules['gecomIDUnique'] = ['gecom_id', 'unique'];

		return $rules;
	}

	/** @inheritdoc */
	public function attributeLabels() {
		$attributeLabels = parent::attributeLabels();
		$attributeLabels['gecom_id'] = 'Gecom ID';
		return $attributeLabels;
	}

	/**
	 * Gets an id => name array.
	 * return string[]
	 */
	public static function getIdNameArray() {
		$customers = ArrayHelper::map(self::find()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
		return $customers;
	}
}
