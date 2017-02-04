<?php

namespace app\models;

use dektrium\user\models\User as BaseUser;

/**
 * @property integer $gecom_id
 */
class User extends BaseUser {

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
}
