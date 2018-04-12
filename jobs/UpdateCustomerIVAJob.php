<?php

namespace app\jobs;

use app\models\Customer;
use yii\queue\JobInterface;
use yii\base\BaseObject;

class UpdateCustomerIVAJob extends BaseObject implements JobInterface {

  public $customerId;

  public function execute($queue)
  {
    $customer = Customer::findOne($this->customerId);
    $iva = $customer->getIvaFromAfipWS();
    $customer->iva = $iva;
    $customer->save(false);
  }

};
