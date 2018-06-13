<?php

namespace app\controllers;

use Yii;
use app\filters\AuthorRule;
use app\filters\DeliveryStatusRule;
use app\models\Delivery;
use app\models\DeliveryIssue;
use app\models\DeliveryOrder;
use app\models\DeliverySearch;
use app\models\DeliveryStatus;
use app\models\Transport;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;

/**
 * DeliveryController implements the CRUD actions for Delivery model.
 */
class DeliveryController extends Controller
{
	/**
	 * @var \app\models\Delivery
	 */
	public $model;

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
					'edit-status' => ['POST'],
					'edit-transport' => ['POST'],
					'edit-tracking-number' => ['POST'],
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
						'actions' => ['edit'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getBodyParam('editableKey'));
						},
						'roles' => ['admin', 'author'],
					],
					[
						'actions' => ['edit-status', 'edit-transport', 'edit-tracking-number', 'delete'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('id'));
						},
						'roles' => ['admin', 'author'],
					],
					[
						'actions' => ['add-order', 'add-issue', 'delete-order', 'delete-issue'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('deliveryId'));
						},
						'roles' => ['admin', 'author'],
					],
					[
						'actions' => ['index', 'create', 'view'],
						'roles' => ['@'],
					],
				],
			],
			'status' => [
				'class' => DeliveryStatusRule::className(),
				'only' => ['add-order', 'add-issue', 'delete-order', 'delete-issue'],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			// This actions is only used by the tracking number editable
			'edit' => [
				'class' => EditableColumnAction::className(),
				'modelClass' => Delivery::className(),
				'scenario' => Delivery::SCENARIO_EDIT_TRACKING_NUMBER,
			],
		]);
	}

	/**
	 * Lists all Delivery models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new DeliverySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Delivery model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * This action is ment to handle the request from the delivery status editable
	 * @param integer $id
	 * @return mixed
	 */
	public function actionEditStatus($id) {
		$model = $this->findModel($id);
		$model->scenario = Delivery::SCENARIO_EDIT_STATUS;
		$out = Json::encode(['output' => '', 'message' => '']);
		$editableIndex = Yii::$app->request->post('editableIndex', null);
		if ($editableIndex !== null) {
			$postModel = Yii::$app->request->post('Delivery')[$editableIndex];
		} else {
			$postModel = Yii::$app->request->post('Delivery');
		}
		if ($postModel) {
			$model->setAttributes($postModel);
			$model->save();
			$output = DeliveryStatus::statusLabels()[$model->status];
			$out = Json::encode(['output' => $output, 'message' => $model->getErrors('status')]);
		}
		// return ajax json encoded response and exit
		return $out;
	}

	/**
	 * This action is ment to handle the request from the delivery transport editable
	 * @param integer $id
	 * @return mixed
	 */
	public function actionEditTransport($id) {
		$model = $this->findModel($id);
		$model->scenario = Delivery::SCENARIO_EDIT_TRANSPORT;
		$out = Json::encode(['output' => '', 'message' => '']);
		$editableIndex = Yii::$app->request->post('editableIndex', null);
		if ($editableIndex !== null) {
			$postModel = Yii::$app->request->post("Delivery")[$editableIndex];
		} else {
			$postModel = Yii::$app->request->post('Delivery');
		}
		if ($postModel) {
			$model->setAttributes($postModel);
			$model->save();
			$output = Transport::getIdNameArray()[$model->transport_id];
			$out = Json::encode(['output' => $output, 'message' => $model->getErrors('transport')]);
		}
		// return ajax json encoded response and exit
		return $out;
	}

	/**
	 * This action is ment to handle the request from the delivery tracking number editable
	 * @param integer $id
	 * @return mixed
	 */
	public function actionEditTrackingNumber($id) {
		$model = $this->findModel($id);
		$model->scenario = Delivery::SCENARIO_EDIT_TRACKING_NUMBER;
		$out = Json::encode(['output' => '', 'message' => '']);
		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				$model->sendEmail();
			}
			$out = Json::encode(['output' => '', 'message' => $model->getErrors('tracking_number')]);
		}
		// return ajax json encoded response and exit
		echo $out;
		return;
	}

	/**
	 * Creates a new Delivery model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @param int[] $orderIds
	 * @param int[] $issueIds
	 * @return mixed
	 */
	public function actionCreate(array $orderIds = [], array $issueIds = [])
	{
		$model = new Delivery();
		$model->save();

		foreach ($orderIds as $id) {
			$entry = new DeliveryOrder();
			$entry->order_id = $id;
			$entry->delivery_id = $model->id;
			$entry->save();
		}

		foreach ($issueIds as $id) {
			$entry = new DeliveryIssue();
			$entry->issue_id = $id;
			$entry->delivery_id = $model->id;
			$entry->save();
		}

		return $this->redirect(['view', 'id' => $model->id]);
	}

	/**
	 * Deletes an existing Delivery model.
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
	 * Adds an order to an existing Delivery.
	 * @param integer $deliveryId
	 */
	public function actionAddOrder($deliveryId) {
		$delivery = $this->findModel($deliveryId);
		$model = new DeliveryOrder();
		$model->delivery_id = $delivery->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ['success' => true];
		} else {
			return $this->renderAjax('add-order', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Adds an issue to an existing Delivery.
	 * @param integer $deliveryId
	 */
	public function actionAddIssue($deliveryId) {
		$delivery = $this->findModel($deliveryId);
		$model = new DeliveryIssue();
		$model->delivery_id = $delivery->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ['success' => true];
		} else {
			return $this->renderAjax('add-issue', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing DeliveryOrder model.
	 * @param integer $deliveryId
	 * @param integer $orderId
	 * @return mixed
	 */
	public function actionDeleteOrder($deliveryId, $orderId)
	{
		$this->findDeliveryOrderModel($deliveryId, $orderId)->delete();
		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['success' => true];
	}

	/**
	 * Deletes an existing DeliveryIssue model.
	 * @param integer $deliveryId
	 * @param integer $issueId
	 * @return mixed
	 */
	public function actionDeleteIssue($deliveryId, $issueId)
	{
		$this->findDeliveryIssueModel($deliveryId, $issueId)->delete();
		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['success' => true];
	}

	/**
	 * Finds the Delivery model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Delivery the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (!$this->model) {
			$this->model = Delivery::find()->andWhere(['id' => $id])->with([
				'orders.orderStatus',
				'issues.issueStatus',
			])->one();
		}
		if ($this->model !== null) {
			return $this->model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Finds the DeliveryOrder model.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $deliveryId
	 * @param integer $orderId
	 * @return DeliveryOrder the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findDeliveryOrderModel($deliveryId, $orderId) {
		if (($model = DeliveryOrder::findOne(['delivery_id' => $deliveryId, 'order_id' => $orderId])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Finds the DeliveryIssue model.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $deliveryId
	 * @param integer $issueId
	 * @return DeliveryIssue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findDeliveryIssueModel($deliveryId, $issueId) {
		if (($model = DeliveryIssue::findOne(['delivery_id' => $deliveryId, 'issue_id' => $issueId])) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
