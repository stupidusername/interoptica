<?php

namespace app\controllers\api;

use app\models\api\Pagination;
use app\models\api\PaginatedItems;
use app\models\ApiKey;
use app\models\Model;
use app\models\Order;
use app\models\OrderStatus;
use dektrium\user\filters\AccessRule;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

class OrderController extends BaseController {

    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'roles' => ['admin', 'api_client'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

  public function actionList($updated_since = null, $user = null, int $page = 1, int $pagelen = 100) {
      // Validate params.
      if ($page < 1) {
          throw new BadRequestHttpException('Page cannot be lower than 1.');
      }
      if ($pagelen < 1 || $pagelen > 100) {
          throw new BadRequestHttpException('The specified pagelen must take a value between 1 and 100.');
      }
      // Validate date format.
    if ($updated_since && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $updated_since)) {
      throw new BadRequestHttpException('updated_since must be of the format yyyy-mm-dd');
    }
    // Retrieved deleted records
    $where = [];
    if ($updated_since) {
      $where = ['or', ['and', ['not', ['or', ['order.deleted' => null], ['order.deleted' => 0]]], ['>=', 'delete_datetime', $updated_since]], ['>=', 'create_datetime', $updated_since]];
    }
    $query = Order::find()->where($where)->joinWith([
      'orderStatus',
      'orderCondition',
      'user' => function ($query) use ($user) {
          $query->andFilterWhere(['username' => $user]);
      },
    ])->with([
      'customer' => function ($query) {
        // Retrieve deleted records
        $query->where([]);
      },

      'orderProducts' => function ($query) {
      },
      'orderProducts.product',
      'orderProducts.product.model',
      'orderProducts.orderProductBatches.batch',
    ]);
    $models = $query->asArray()->limit($pagelen)->offset(($page - 1) * $pagelen)->all();
    $orders = [];
    foreach ($models as $model) {
      $subtotal = 0;
      $items = [];
      foreach ($model['orderProducts'] as $orderProduct) {
        $subtotal += $orderProduct['quantity'] * $orderProduct['price'];
        $batches = [];
        foreach ($orderProduct['orderProductBatches'] as $orderProductBatch) {
          $batches[] = [
            'id' => (int) $orderProductBatch['id'],
            'dispatch_number' => $orderProductBatch['batch']['dispatch_number'],
            'quantity' => (int) $orderProductBatch['quantity'],
          ];
        }
        $items[] = [
          'id' => (int) $orderProduct['product']['id'],
          'code' => $orderProduct['product']['code'],
          'title' => $orderProduct['product']['model']['name'],
          'unit_price' => (float) $orderProduct['price'],
          'quantity' => (float) $orderProduct['quantity'],
          'imported' => $orderProduct['product']['model']['origin'] == Model::ORIGIN_IMPORTED,
          'batches' => $batches,
        ];
      }
      $subtotal *= (1 - (float) $model['discount_percentage'] / 100);
      $discount = $subtotal / (1 - (float) $model['discount_percentage'] / 100) - $subtotal;
      $subtotalPlusIva = $subtotal * (1 + (float) $model['iva'] / 100);
      $financing = $subtotalPlusIva * ((float) $model['interest_rate_percentage'] / 100);
      $total = $subtotalPlusIva + $financing;
      $orders[] = [
        'id' => (int) $model['id'],
        'user' => $model['user']['username'],
        'status' => $model['deleted'] ? 'deleted' : [
          OrderStatus::STATUS_LOADING => 'loading',
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
        'iva' => (float) $model['iva'],
        'discount_percentage' => (float) $model['discount_percentage'],
        'condition' => $model['orderCondition']['title'],
        'interest_rate_percentage' => (float) $model['interest_rate_percentage'],
        'subtotal' => $subtotal,
        'discount' => $discount,
        'subtotal_plus_iva' => $subtotalPlusIva,
        'financing' => $financing,
        'total' => $total,
          'customer' => [
            'id' => (int) $model['customer']['id'],
            'gecom_id' => (int) $model['customer']['gecom_id'],
            'name' => $model['customer']['name'],
            'email' => $model['customer']['email'],
            'cuit' => $model['customer']['cuit'],
            'tax_situation' => $model['customer']['tax_situation'],
            'tax_situation_category' => $model['customer']['tax_situation_category'],
            'phone_number' => $model['customer']['phone_number'],
          ],
          'items' => $items,
      ];
    }
    // Build pagination.
    $totalItems = (integer) $query->count();
    $totalPages = ceil($totalItems / $pagelen);
    $nextPage = $page < $totalPages ? Url::to(['list', 'updated_since' => $updated_since, 'user' => $user, 'page' => $page + 1, 'pagelen' => $pagelen], true) : null;
    $prevPage = $page > 1 ? Url::to(['list', 'updated_since' => $updated_since, 'user' => $user, 'page' => $page - 1, 'pagelen' => $pagelen], true) : null;
    $pagination = new Pagination([
        'next' => $nextPage,
        'prev' => $prevPage,
        'pagelen' => $pagelen,
        'page' => $page,
        'totalItems' => $totalItems,
        'totalPages' => $totalPages,
    ]);
    $paginatedItems = new PaginatedItems([
        'pagination' => $pagination,
        'items' => $orders,
    ]);
    return $paginatedItems;
  }

}
