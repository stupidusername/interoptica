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
			[['customerEmail'], 'uniqueEmail'],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return array_merge(parent::attributeLabels(), [
			'customerEmail' => 'Email del cliente',
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

	/**
	 * @param string $attribute the attribute currently being validated
	 * @param mixed $params the value of the "params" given in the rule
	 * @param \yii\validators\InlineValidator related InlineValidator instance.
	 * This parameter is available since version 2.0.11.
	 */
	public function uniqueEmail($attribute, $params, $validator) {
		$customer = Customer::find()->andWhere(['email' => $this[$attribute]])->one();
		if ($customer && $customer->id != $this->customer_id) {
			$this->addError($attribute, 'La dirección de email ya se encuentra registrada por otro cliente.');
		}
	}
}
