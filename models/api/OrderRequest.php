<?php

namespace app\models\api;

use app\models\Customer;
use app\models\OrderForm;
use app\models\SalesmanSuitcase;
use app\models\Suitcase;

class OrderRequest extends OrderForm {

    public $customerGecomId;
    public $suitcaseId;

    /**
	 * Scenarios
	 */
	const SCENARIO_CREATE = 'create';

    public function fields() {
        $fields = [
            'customer_email' => 'customerEmail',
            'customer_gecom_id' => 'customerGecomId',
            'suitcase_id' => 'suitcaseId',
        ];
        return array_merge($fields, parent::fields());
    }

    /**
	* @inheritdoc
	*/
	public static function find() {
		return parent::find()->with(['customer']);
	}

    public function afterFind() {
        parent::afterFind();
        if (!$this->customerGecomId) {
            $this->customerGecomId = $this->customer->gecom_id;
        }
    }

    public function rules() {
        $rules = [
            [['customerGecomId'], 'required'],
            [['customerGecomId'], 'string'],
            [['customerGecomId'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customerGecomId' => 'id']],
            [['suitcaseId'], 'integer'],
            [['suitcaseId'], 'exist', 'skipOnError' => true, 'targetClass' => Suitcase::className(), 'targetAttribute' => ['suitcaseId' => 'id']],
            [['suitcaseId'], 'required', 'on' => self::SCENARIO_CREATE],
        ];
        return array_merge($rules, parent::rules());
    }

    public function validate($attributeNames = null, $clearErrors = true) {
        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        $attributeNames = (array) $attributeNames;
        if (($key = array_search('customerGecomId', $attributeNames)) !== false) {
            unset($attributeNames[$key]);
        }

        if ($this->customerGecomId) {
            $customer = Customer::findOne(['gecom_id' => $this->customerGecomId]);
            if ($customer) {
                $this->customer_id = $customer->id;
            }
        }

        if ($this->suitcaseId) {
            $salesmanSuitcase = SalesmanSuitcase::findOne(['customer_id' => $this->customer_id, 'suitcase_id' => $this->suitcaseId]);
            if (!$salesmanSuitcase) {
                $this->addError('suitcaseId', 'La valija no esta asignada a ningun vendedor.');
            } else {
                $this->user_id = $salesmanSuitcase->user_id;
            }
        }

        if ($this->isNewRecord) {
            $this->from_api = true;
        }

        return parent::validate($attributeNames, false);
    }

    public function load($data, $formName = null) {
        foreach ($data as $key => $value) {
            if (isset($this->fields()[$key])) {
                $data[$this->fields()[$key]] = $value;
            }
        }
        return parent::load($data, $formName);
    }

    public function addError($attribute, $error = '') {
        if (($key = array_search($attribute, $this->fields())) !== false) {
            $attribute = $key;
        }
        parent::addError($attribute, $error);
    }
}
