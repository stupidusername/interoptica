<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\Order;
use app\models\OrderForm;
use app\models\OrderInvoice;
use app\models\OrderProduct;
use app\models\OrderProductsForm;
use app\models\OrderStatus;
use app\models\OrderSearch;
use app\models\OrderSummary;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use app\filters\AuthorRule;
use app\filters\OrderStatusRule;
use kartik\mpdf\Pdf;
use yii\helpers\Json;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
	public $model = null;

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
					'delete-entry' => ['POST'],
					'delete-invoice' => ['POST'],
				],
			],
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
						'actions' => ['update', 'delete', 'enter'],
						'model' => function() {
							return $this->findOrderFormModel(Yii::$app->request->getQueryParam('id'));
						},
						'roles' => ['admin', 'author', 'editor'],
					],
					[
						'actions' => ['add-entry', 'update-entry', 'delete-entry'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('orderId'));
						},
						'roles' => ['admin', 'author', 'editor'],
					],
					[
						'actions' => ['add-invoice', 'delete-invoice'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('orderId'));
						},
						'roles' => ['admin', 'author', 'depot', 'editor'],
					],
					[
						'actions' => ['statistics'],
						'roles' => ['admin', 'management'],
					],
					[
						'actions' => ['index', 'view', 'create', 'export-txt', 'export-pdf', 'list', 'send-email'],
						'roles' => ['@'],
					],
				],
			],
			'status' => [
				'class' => OrderStatusRule::className(),
				'only' => ['delete', 'add-entry', 'update-entry', 'delete-entry', 'enter'],
			],
		];
	}

	/**
	 * Lists all Order models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new OrderSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

		// data provider used for export
		$exportDataProvider = $dataProvider;
		if (Yii::$app->request->isPost) {
			$exportDataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$exportDataProvider->pagination->pageSize = 0;
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'exportDataProvider' => $exportDataProvider,
		]);
	}

	/**
	 * Displays a single Order model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		$model = $this->findModel($id);
		if (Yii::$app->request->isPost && Yii::$app->request->getBodyParam('Order')) {
			$model->scenario = Order::SCENARIO_VIEW;
			$editableIndex = Yii::$app->request->post('editableIndex', null);
			if ($editableIndex !== null) {
				$postModel = Yii::$app->request->post('Order')[$editableIndex];
			} else {
				$postModel = Yii::$app->request->post('Order');
			}
			$model->setAttributes($postModel);
			$model->save();
			$output = OrderStatus::statusLabels()[$model->status];
			$out = ['output' => $output, 'message' => $model->getErrors('status')];
			// return ajax json encoded response and exit
			Yii::$app->response->format = Response::FORMAT_JSON;
			return $out;
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Sets the status of a model to OrderStatus::STATUS_ENTERED
	 * @param integer $id
	 * @return mixed
	 */
	public function actionEnter($id) {
		$model = $this->findModel($id);
		$model->status = OrderStatus::STATUS_ENTERED;
		$model->save();
		$this->redirect(['view', 'id' => $id]);
	}

	/**
	 * Creates a new Order model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new OrderForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Order model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findOrderFormModel($id);
		$model->scenario = OrderForm::SCENARIO_UPDATE;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Order model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Adds a product to an existing Order.
	 * @param integer $orderId
	 */
	public function actionAddEntry($orderId) {
		$order = $this->findModel($orderId);
		$model = new OrderProductsForm();
		$model->orderId = $order->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ['success' => true];
		} else {
			return $this->renderAjax('add-entry', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing OrderProduct model.
	 * @param integer $orderId
	 * @param integer $productId
	 */
	public function actionUpdateEntry($orderId, $productId) {
		$model = $this->findOrderProductModel($orderId, $productId);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ['success' => true];
		} else {
			return $this->renderAjax('update-entry', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing OrderProduct model.
	 * @param integer $orderId
	 * @param integer $productId
	 * @return mixed
	 */
	public function actionDeleteEntry($orderId, $productId)
	{
		$this->findOrderProductModel($orderId, $productId)->delete();
		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['success' => true];
	}

	/**
	 * Adds an invoice  to an existing Order.
	 * @param integer $orderId
	 */
	public function actionAddInvoice($orderId) {
		$order = $this->findModel($orderId);
		$model = new OrderInvoice();
		$model->order_id = $order->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ['success' => true];
		} else {
			return $this->renderAjax('/invoice/add', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing OrderInvoice model.
	 * @param integer $orderId
	 * @param integer $invoiceId
	 * @return mixed
	 */
	public function actionDeleteInvoice($orderId, $invoiceId)
	{
		OrderInvoice::deleteAll(['id' => $invoiceId]);
		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['success' => true];
	}

	/**
	 * Exports an existing Order model to txt.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionExportTxt($id) {
		$model = $this->findModel($id);
		return Yii::$app->response->sendContentAsFile($model->getTxt(), $model->getTxtName(), ['mimeType' => 'text/plain']);
	}

	/**
	 * Exports an existing Order model to pdf.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionExportPdf($id) {
		return Order::getPdf($id, Pdf::DEST_DOWNLOAD);
	}

	/**
	 * Sends the order pdf to the customer email.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionSendEmail($id) {
		$pdfContent = Order::getPdf($id, Pdf::DEST_STRING);

		$order = $this->findModel($id);

		$success = Yii::$app->mailer->compose('order/confirmed')
			->setFrom($order->user->email)
			->setTo($order->customer->email)
			->setSubject('Nota de pedido')
			->attachContent($pdfContent, ['fileName' => $order->id . '.pdf', 'contentType' => 'application/pdf'])
			->send();

			return $this->render('mail-sent', ['model' => $order, 'success' => $success]);
	}

	public function actionStatistics() {

		$orderModel = new OrderSummary();
		$ordersBySalesman = $orderModel->search(Yii::$app->request->queryParams)->query->all();
		$ordersByBrand = $orderModel->searchByBrand(Yii::$app->request->queryParams)->query->all();
		return $this->render('statistics', [
			'orderModel' => $orderModel,
			'ordersBySalesman' => $ordersBySalesman,
			'ordersByBrand' => $ordersByBrand,
		]);
	}

	/**
	 * Finds the Order model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Order the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if ($this->model || ($this->model = Order::find()->andWhere(['id' => $id])->with('orderProducts.product.model')->one()) !== null) {
			return $this->model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Finds the OrderForm model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return OrderForm the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findOrderFormModel($id)
	{
		if ($this->model || ($this->model = OrderForm::findOne($id)) !== null) {
			return $this->model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Finds the OrderProduct model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $orderId
	 * @param integer $productId
	 * @return OrderProduct the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findOrderProductModel($orderId, $productId)
	{
		if (($model = OrderProduct::findOne(['order_id' => $orderId, 'product_id' => $productId])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Builds a response for Select2 order widget on delivery view
	 * @param string $q
	 * @return JSON
	 */
	public function actionList($q = '') {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$ordersArray = Order::find()->andWhere([
			'or',
			[Order::tableName() . '.id' => $q],
			[Customer::tableName() . '.gecom_id' => $q],
			['like', Customer::tableName() . '.name', $q],
		])->innerJoinWith([
			'customer',
			'orderStatus' => function ($query) {
				$query->andWhere(['<', 'status', OrderStatus::STATUS_WAITING_FOR_TRANSPORT]);
			}
		])->orderBy(['id' => SORT_DESC])->asArray()->all();

		$results = array_map(function ($array) {
			return [
				'id' => $array['id'],
				'text' => 'Pedido: ' . $array['id'] . ' (' . OrderStatus::statusLabels()[$array['orderStatus']['status']]  . ') - Cliente: ' . $array['customer']['name']];
		}, $ordersArray);
		$out = ['results' => $results];
		return $out;
	}
}
