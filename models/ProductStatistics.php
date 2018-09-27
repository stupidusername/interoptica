<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class ProductStatistics extends Product {

  public $orderStatus = OrderStatus::STATUS_ENTERED;
  public $fromDate;
  public $toDate;
  public $brandId;
  public $modelType;
  public $modelName;

  public function search($params) {
    $query = Product::find()->active();

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    return $dataProvider;
  }
}
