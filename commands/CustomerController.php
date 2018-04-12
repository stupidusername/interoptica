<?php

namespace app\commands;

use app\jobs\UpdateCustomerIVAJob;
use app\models\Customer;
use Yii;
use yii\console\Controller;

class CustomerController extends Controller
{
    public function actionUpdateIva()
    {
        $customers = Customer::find()->select('id')->asArray()->where(['or', ['deleted' => null], ['deleted' => 0]])->all();
        foreach ($customers as $customer) {
          Yii::$app->queue->push(new UpdateCustomerIVAJob(['customerId' => $customer['id']]));
        }
    }
}
