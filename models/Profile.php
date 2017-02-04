<?php

namespace app\models;

use dektrium\user\models\Profile as BaseProfile;

/**
 * @property string $phone_number
 */
class Profile extends BaseProfile {

	/** @inheritdoc */
	public function rules() {
		$rules = parent::rules();
		// add some rules
		$rules['phoneNumberLength'] = ['phone_number', 'string', 'max' => 255];

		return $rules;
	}

	/** @inheritdoc */
	public function attributeLabels() {
		$attributeLabels = parent::attributeLabels();
		$attributeLabels['phone_number'] = 'Teléfono';
		return $attributeLabels;
	}
}
