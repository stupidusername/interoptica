<?php

namespace app\models;

use app\components\DeletedQuery;
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

	/**
	* @inheritdoc
	*/
	public static function find() {
		return new DeletedQuery(get_called_class());
	}

	/** @inheritdoc */
	public function scenarios() {
		$scenarios = parent::scenarios();
		// add field to scenarios
		$scenarios['create'][] = 'gecom_id';
		$scenarios['update'][] = 'gecom_id';
		$scenarios['register'][] = 'gecom_id';
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
	 * @return string
	 */
	public function getDisplayName() {
		return implode(' - ', [$this->gecom_id, $this->profile->name ?? $this->username]);
	}

	/**
	 * Gets an id => name array.
	 * return string[]
	 */
	public static function getIdNameArray() {
		$customers = ArrayHelper::map(self::find()->active()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
		return $customers;
	}
}
