<?php

namespace app\models;

use yii\base\Exception;

class OrderForm extends Order {

	/**
	 * @var string
	 */
	public $customerEmail;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return array_merge(parent::rules(), [
			[['customerEmail'], 'required'],
			[['customerEmail'], 'email'],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return array_merge(parent::attributeLabels(), [
			'customerEmail' => 'Email del Cliente',
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->customerEmail = $this->customer_id && $this->customer ? $this->customer->email : null;
		parent::afterFind();
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		if ($insert || $this->customer->email != $this->customerEmail) {
			$this->customer->email = $this->customerEmail;
			if(!$this->customer->save(false)) {
				throw new Exception('Error saving customer email.');
			};
		}
		parent::afterSave($insert, $changedAttributes);
	}
}
