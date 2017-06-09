<?php

namespace app\controllers;

use Yii;
use app\filters\AuthorRule;
use app\models\Delivery;
use app\models\DeliveryIssue;
use app\models\DeliveryOrder;
use app\models\DeliverySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
						'actions' => ['edit', 'delete', 'add-order', 'add-issue', 'delete-order', 'delete-issue'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('id'));
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
				'only' => ['edit', 'delete', 'add-order', 'add-issue', 'delete-order', 'delete-issue'],
			],
		];
	}

	/**
	 * Lists all Delivery models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new DeliverySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
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
	 * Finds the Delivery model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Delivery the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if ($this->model || ($this->model = Delivery::findOne($id)) !== null) {
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
