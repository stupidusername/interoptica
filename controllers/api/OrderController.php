<?php

namespace app\controllers\api;

use app\filters\AuthorRule;
use app\filters\OrderStatusRule;
use app\models\api\OrderProductRequest;
use app\models\api\OrderRequest;
use app\models\api\Pagination;
use app\models\api\PaginatedItems;
use app\models\ApiKey;
use app\models\Model;
use app\models\Order;
use app\models\OrderInvoice;
use app\models\OrderStatus;
use dektrium\user\filters\AccessRule;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class OrderController extends BaseController {

    public $model = null;

    public function behaviors()
    {
        $behaviors = [
            'access' => [
				'class' => AccessControl::className(),
				'ruleConfig' => [
					'class' => AuthorRule::className(),
					'modelField' => 'model',
					'authorIdAttribute' => 'user_id',
					'allow' => true,
				],
				'rules' => [
                    [
						'actions' => ['list', 'get', 'create'],
						'roles' => ['admin', 'api_client'],
					],
                    [
						'actions' => ['update', 'delete', 'cancel', 'fail'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('id'));
						},
						'roles' => ['admin', 'author'],
					],
                    [
						'actions' => ['add-item', 'update-item', 'delete-item', 'add-invoice', 'delete-invoice'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('orderId'));
						},
						'roles' => ['admin', 'author'],
					],
				],
			],
			'status' => [
				'class' => OrderStatusRule::className(),
				'only' => ['delete', 'add-item', 'update-item', 'delete-item'],
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
    // Retrieve deleted records
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
      'orderInvoices',
      'transport',
    ]);
    $models = $query->asArray()->limit($pagelen)->offset(($page - 1) * $pagelen)->all();
    $orders = [];
    foreach ($models as $model) {
      $orders[] = $this->serialize($model);
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

  public function actionGet($id) {
      $order = $this->findModel($id);
      return $this->serialize($order);
  }

  public function actionCreate() {
      $model = new OrderRequest();
      $model->load(Yii::$app->getRequest()->getBodyParams(), '');
      if ($model->save()) {
          $response = Yii::$app->getResponse();
          $response->setStatusCode(201);
          $response->getHeaders()->set('Location', Url::toRoute(['get', 'id' => $model->id], true));
      } elseif (!$model->hasErrors()) {
          throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
      }
      return $model;
  }

  public function actionUpdate($id) {
      $model = $this->findOrderRequestModel($id);
      $model->scenario = OrderRequest::SCENARIO_UPDATE;
      $params = Yii::$app->getRequest()->getBodyParams();
      if (isset($params['status'])) {
          $params['status'] = [
              'loading' => OrderStatus::STATUS_LOADING,
              'entered' => OrderStatus::STATUS_ENTERED,
              'collect' => OrderStatus::STATUS_COLLECT,
              'collect_revision' => OrderStatus::STATUS_COLLECT_REVISION,
              'administration' => OrderStatus::STATUS_ADMINISTRATION,
              'pending_put_together' => OrderStatus::STATUS_PENDING_PUT_TOGETHER,
              'put_together' => OrderStatus::STATUS_PUT_TOGETHER,
              'put_together_printed' => OrderStatus::STATUS_PUT_TOGETHER_PRINTED,
              'billing' => OrderStatus::STATUS_BILLING,
              'packaging' => OrderStatus::STATUS_PACKAGING,
              'waiting_for_transport' => OrderStatus::STATUS_WAITING_FOR_TRANSPORT,
              'sent' => OrderStatus::STATUS_SENT,
              'delivered' => OrderStatus::STATUS_DELIVERED,
          ][$params['status']];
      }
      $model->load($params, '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
      return $model;
  }

  public function actionDelete($id) {
      $order = $this->findModel($id);
      $order->delete();
  }

  public function actionCancel($id) {
      $order = $this->findModel($id);
      $order->delete_reason = Order::DELETE_REASON_CANCELED;
      $order->save();
      $order->delete();
  }

  public function actionFail($id) {
      $order = $this->findModel($id);
      $order->delete_reason = Order::DELETE_REASON_FAILED;
      $order->save();
      $order->delete();
  }

  public function actionAddItem($orderId) {
      $model = new OrderProductRequest();
      $model->order_id = $orderId;
      $model->load(Yii::$app->getRequest()->getBodyParams(), '');
      if ($model->save()) {
          $response = Yii::$app->getResponse();
          $response->setStatusCode(201);
      } elseif (!$model->hasErrors()) {
          throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
      }
      return $model;
  }

  public function actionUpdateItem($orderId, $itemId) {
      $model = $this->findOrderProductRequestModel($orderId, $itemId);
      $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
      return $model;
  }

  public function actionDeleteItem($orderId, $itemId) {
      $orderProduct = $this->findOrderProductRequestModel($orderId, $itemId);
      $orderProduct->delete();
  }

  public function actionAddInvoice($orderId) {
    $model = new OrderInvoice();
    $model->order_id = $orderId;
    $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    if ($model->save()) {
        $response = Yii::$app->getResponse();
        $response->setStatusCode(201);
    } elseif (!$model->hasErrors()) {
        throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
    }
    return $model;
  }

  public function actionDeleteInvoice($orderId, $invoiceId) {
      $orderInvoice = $this->findOrderInvoiceModel($orderId, $invoiceId);
      $orderInvoice->delete();
  }

  /**
   * Finds the Order model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Order the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  private function findModel($id) {
      if (!$this->model) {
          $this->model = Order::find()->where(['order.id' => $id])->joinWith([
            'orderStatus',
            'orderCondition',
            'user',
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
            'orderInvoices',
            'transport',
          ])->one();
      }
      if (!$this->model) {
          throw new NotFoundHttpException('The requested order does not exist.');
      }
      return $this->model;
  }

  private function findOrderRequestModel($id)
  {
      if ($this->model || ($this->model = OrderRequest::findOne($id)) !== null) {
          return $this->model;
      } else {
          throw new NotFoundHttpException('The requested page does not exist.');
      }
  }

  private function findOrderProductRequestModel($orderId, $orderProductId) {
      $orderProduct = OrderProductRequest::findOne(['id' => $orderProductId, 'order_id' => $orderId]);
      if (!$orderProduct) {
          throw new NotFoundHttpException('The requested order item does not exist.');
      }
      return $orderProduct;
  }

  private function findOrderInvoiceModel($orderId, $orderInvoiceId) {
      $orderInvoice = OrderInvoice::findOne(['id' => $orderInvoiceId, 'order_id' => $orderId]);
      if (!$orderInvoice) {
          throw new NotFoundHttpException('The requested order invoice does not exist.');
      }
      return $orderInvoice;
  }

  private function serialize($model) {
      $subtotal = 0;
      $invoices = [];
      foreach ($model['orderInvoices'] as $orderInvoice) {
        $invoices[] = [
          'id' => (int) $orderInvoice['id'],
          'number' => $orderInvoice['number'],
          'comment' => $orderInvoice['comment'],
        ];
      }
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
          'id' => (int) $orderProduct['id'],
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
      $order = [
        'id' => (int) $model['id'],
        'user' => $model['user']['username'],
        'status' => $model['deleted'] ? ($model['delete_reason'] ? $model['delete_reason'] : 'deleted') : [
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
        'delivery_address' => $model['delivery_address'],
        'delivery_city' => $model['delivery_city'],
        'delivery_state' => $model['delivery_state'],
        'delivery_zip_code' => $model['delivery_zip_code'],
        'iva' => (float) $model['iva'],
        'discount_percentage' => (float) $model['discount_percentage'],
        'condition' => [
                'id' => $model['orderCondition']['id'],
                'title' => $model['orderCondition']['title'],
        ],
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
            'address' => $model['customer']['address'],
            'zip_code' => $model['customer']['zip_code'],
            'province' => $model['customer']['province'],
            'locality' => $model['customer']['locality'],
            'phone_number' => $model['customer']['phone_number'],
          ],
          'transport' => [
              'id' => $model['transport']['id'],
              'name' => $model['transport']['name'],
          ],
          'invoices' => $invoices,
          'items' => $items,
      ];
      return $order;
  }

}
