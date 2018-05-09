<?php

namespace app\controllers;

use app\models\ApiKey;
use app\models\Order;
use app\models\OrderStatus;
use Yii;

class ApiController extends \yii\rest\Controller {

  /**
  *  @inheritdoc
  */
  public function beforeAction($action) {
    if (!parent::beforeAction($action)) {
      return false;
    }
    $key = Yii::$app->request->getQueryParam('key');
    if ($key && ApiKey::findOne(['key' => $key])) {
      return true;
    } else {
        throw new \yii\web\UnauthorizedHttpException;
    }
  }

  public function actionGetOrders($updated_since = null, $page = 0) {
    if ($updated_since && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $updated_since)) {
      throw new \yii\web\BadRequestHttpException('updated_since must be of the format yyyy-mm-dd');
    }
    $pageSize = 100;
    // Retrieved deleted records
    $where = [];
    if ($updated_since) {
      $where = ['or', ['and', ['not', ['or', ['deleted' => null], ['deleted' => 0]]], ['>=', 'delete_datetime', $updated_since]], ['>=', 'create_datetime', $updated_since]];
    }
    $query = Order::find()->where($where)->joinWith([
      'orderStatus',
    ])->with([
      'customer' => function ($query) {
        // Retrieve deleted records
        $query->where([]);
      },

      'orderProducts' => function ($query) {
      },
      'orderProducts.product',
      'orderProducts.product.model',
    ]);
    $pages = ceil($query->count() / $pageSize);
    $models = $query->asArray()->limit($pageSize)->offset($page * $pageSize)->all();
    $orders = [];
    foreach ($models as $model) {
      $subtotal = 0;
      $items = [];
      foreach ($model['orderProducts'] as $orderProduct) {
        $subtotal += $orderProduct['quantity'] * $orderProduct['price'];
        $items[] = [
          'id' => (int) $orderProduct['product']['id'],
          'code' => $orderProduct['product']['code'],
          'title' => $orderProduct['product']['model']['name'],
          'unit_price' => (float) $orderProduct['price'],
          'quantity' => (float) $orderProduct['quantity'],
        ];
      }
      $orders[] = [
        'id' => (int) $model['id'],
        'status' => $model['deleted'] ? 'deleted' : [
          OrderStatus::STATUS_ENTERED => 'entered',
          OrderStatus::STATUS_COLLECT => 'collect',
          OrderStatus::STATUS_COLLECT_REVISION => 'collect_revision',
          OrderStatus::STATUS_ADMINISTRATION => 'administration',
          OrderStatus::STATUS_PENDING_PUT_TOGETHER => 'pending_put_together',
          OrderStatus::STATUS_PUT_TOGETHER => 'put_together',
          OrderStatus::STATUS_PUT_TOGETHER_PRINTED => 'put_together_printed',
          OrderStatus::STATUS_BILLING => 'billing',
          OrderStatus::STATUS_PACKAGING => 'packaging',
          OrderStatus::STATUS_WAITING_FOR_TRANSPORT => 'waiting_for_transport',
          OrderStatus::STATUS_SENT => 'sent',
          OrderStatus::STATUS_DELIVERED => 'delivered',
        ][$model['orderStatus']['status']],
        'status_update_datetime' => str_replace(' ', 'T', $model['deleted'] ? $model['delete_datetime'] : $model['orderStatus']['create_datetime']) . 'Z',
        'discount_percentage' => (float) $model['discount_percentage'],
        'subtotal' => $subtotal,
        'total' => $subtotal * (1 - $model['discount_percentage'] / 100),
        'include_iva' => (bool) $model['iva'],
        'relationships' => [
          'customer' => [
            'id' => (int) $model['customer']['id'],
            'gecom_id' => (int) $model['customer']['gecom_id'],
            'name' => $model['customer']['name'],
            'email' => $model['customer']['email'],
            'cuit' => $model['customer']['cuit'],
            'phone_number' => $model['customer']['phone_number'],
          ],
          'items' => $items,
        ],
      ];
    }
    $response = [
      'meta' => [
        'total-pages' => $pages,
      ],
      'data' => $orders,
    ];
    return $response;
  }

}