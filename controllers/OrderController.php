<?php

namespace app\controllers;

use Yii;
use app\models\Order;
use app\models\OrderSearch;
use app\models\OrderProduct;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use app\filters\AuthorRule;

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
                        'actions' => ['update', 'delete'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('id'));
						},
						'roles' => ['admin', 'author'],
                    ],
					[
                        'actions' => ['add-entry', 'update-entry', 'delete-entry'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('orderId'));
						},
						'roles' => ['admin', 'author'],
                    ],
                    [
						'actions' => ['index', 'view', 'create', 'export-txt', 'export-pdf'],
                        'roles' => ['@'],
                    ],
                ],
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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

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
        $model = $this->findModel($id);
		$model->scenario = Order::SCENARIO_UPDATE;

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
		$model = new OrderProduct();
		$model->order_id = $order->id;
		
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
     * If deletion is successful, the browser will be redirected to the 'view' page.
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
	 * Exports an existing Order model to txt.
	 * @param type $id
	 * @return mixed
	 */
	public function actionExportTxt($id) {
		$model = $this->findModel($id);
		return Yii::$app->response->sendContentAsFile($model->getTxt(), $model->getTxtName(), ['mimeType' => 'text/plain']);
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
        if ($this->model || ($this->model = Order::findOne($id)) !== null) {
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
}
