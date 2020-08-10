<?php

namespace app\models\api;

use app\models\OrderProduct;
use app\models\Product;

class OrderProductRequest extends OrderProduct {

    public $code;

    public function rules() {
        $rules = [
            [['code'], 'required'],
            [['code'], 'string'],
            [['code'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className()],
            [['code'], 'uniqueEntry'],
        ];
        return array_merge($rules, parent::rules());
    }

    public function validate($attributeNames = null, $clearErrors = true) {
        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        $attributeNames = (array)$attributeNames;
        if (($key = array_search('product_id', $attributeNames)) !== false) {
            unset($attributeNames[$key]);
        }

        if ($this->code) {
            $product = Product::findOne(['code' => $this->code]);
            if ($product) {
                $this->product_id = $product->id;
            }
        }
        return parent::validate($attributeNames, $clearErrors);
    }
}
