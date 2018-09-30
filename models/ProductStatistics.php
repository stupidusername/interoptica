<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class ProductStatistics extends Product {

  // Search attributes
  public $orderStatus;
  public $fromDate;
  public $toDate;
  public $brandId;
  public $modelType;
  public $modelName;

  // Stat attribute
  public $totalQuantity;
  public $averagePrice;

  public function rules() {
    return [
      [['orderStatus', 'brandId', 'modelType', 'modelName', 'code'], 'safe'],
      [['fromDate', 'toDate'], 'date', 'format' => 'php: Y-m-d'],
    ];
  }

  public function attributeLabels() {
    $labels = parent::attributeLabels();
    $labels['orderStatus'] = 'Estado del pedido';
    $labels['fromDate'] = 'Desde';
    $labels['toDate'] = 'Hasta';
    $labels['totalQuantity'] = 'Cantidad';
    $labels['averagePrice'] = 'Precio Promedio';
    return $labels;
  }

  public function search($params) {
    $this->load($params);

    $query = self::find()->active();

    if ($this->orderStatus !== null && $this->orderStatus !== '') {
      $subQuery = OrderStatus::find()->select('MAX(id)')->groupBy('order_id');
      $query->innerJoin(
        'order_status orderStatus',
        [
          'AND',
          ['orderStatus.id' => $subQuery],
          'orderStatus.status = :status AND orderStatus.order_id = order_product.order_id AND order_product.product_id = product.id',
        ],
        [':status' => $this->orderStatus]
      );
    }

    if ($this->fromDate || $this->toDate) {
      $query->innerJoinWith([
        'orderProducts.order.enteredOrderStatus' => function ($query) {
          if ($this->fromDate) {
            $query->andWhere(['>=', 'order_status.create_datetime', gmdate('Y-m-d', strtotime($this->fromDate))]);
          }
          if ($this->toDate) {
            $toDate = gmdate('Y-m-d', strtotime($this->toDate . ' +1 day'));
            $query->andWhere(['<', 'order_status.create_datetime', $toDate]);
          }
        }
      ], false);
    }

    $query->innerJoinWith([
      'model' => function ($query) {
        $query->andFilterWhere(['type' => $this->modelType]);
        $query->andFilterWhere(['like', Model::tableName() . '.name', $this->modelName]);
      },
      'model.brand' => function ($query) {
        $query->andFilterWhere([Brand::tableName() . '.id' => $this->brandId]);
      },
    ]);

    $query->innerJoinWith([
      'orderProducts.order',
      'orderProducts.orderProductBatches',
    ], false);

    $query->addSelect([
      'totalQuantity' => 'SUM(quantity)',
      'averagePrice' => 'SUM(' . OrderProduct::tableName() . '.price * quantity) / SUM(quantity)',
    ]);

    $query->groupBy([Product::tableName() . '.id']);

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'sort' => [
          'defaultOrder' => ['totalQuantity' => SORT_DESC],
          'attributes' => [
            'code',
            'totalQuantity' => [
              'asc' => ['SUM(quantity)' => SORT_ASC],
              'desc' => ['SUM(quantity)' => SORT_DESC],
            ],
          ]
        ],
    ]);

    return $dataProvider;
  }
}
