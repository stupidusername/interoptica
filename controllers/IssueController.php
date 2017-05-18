<?php

namespace app\controllers;

use app\filters\AuthorRule;
use app\filters\IssueStatusRule;
use app\models\Issue;
use app\models\IssueComment;
use app\models\IssueProduct;
use app\models\IssueProductSearch;
use app\models\IssueSearch;
use app\models\IssueStatus;
use app\models\Order;
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
/**
 * IssueController implements the CRUD actions for Issue model.
 */
class IssueController extends Controller
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
							return $this->findModel(Yii::$app->request->getQueryParam('issueId'));
						},
						'roles' => ['admin', 'author'],
                    ],
					[
						'actions' => ['index', 'view', 'statistics', 'fail-summary', 'create', 'add-comment', 'get-envelope'],
                        'roles' => ['@'],
                    ],
                ],
            ],
			'status' => [
                'class' => IssueStatusRule::className(),
                'only' => ['delete', 'add-entry', 'update-entry', 'delete-entry'],
            ],
        ];
    }

    /**
     * Lists all Issue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IssueSearch();
		$searchModel->user_id = Yii::$app->user->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	 /**
     * Render fail statistics.
     * @return mixed
     */
    public function actionStatistics()
    {
        return $this->render('statistics');
    }
	
	/**
     * Lists all IssueProduct models.
     * @return mixed
     */
    public function actionFailSummary()
    {
        $searchModel = new IssueProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
		
		// data provider used for export
		$exportDataProvider = $dataProvider;
		if (Yii::$app->request->isPost) {
			$exportDataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$exportDataProvider->pagination->pageSize = 0;
		}
		
        return $this->render('fail-summary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'exportDataProvider' => $exportDataProvider,
        ]);
    }

    /**
     * Displays a single Issue model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$model = $this->findModel($id);
		if (Yii::$app->request->isPost) {
			$model->scenario = Issue::SCENARIO_VIEW;
			$out = Json::encode(['output' => '', 'message' => '']);
			if ($model->load(Yii::$app->request->post())) {
				$model->save();
				$output = IssueStatus::statusLabels()[$model->status];
				$out = Json::encode(['output' => $output, 'message' => $model->getErrors()]);
			}
			// return ajax json encoded response and exit
			echo $out;
			return;
		}
		
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Issue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
	 * @param integer $orderId
     * @return mixed
     */
    public function actionCreate($orderId = null)
    {
		$model = new Issue();
		$order = Order::findOne($orderId);
		if ($order) {
			$model->customer_id = $order->customer_id;
			$model->order_id = $order->id;
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Issue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Issue model.
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
	 * Adds a product to an existing Issue.
	 * @param integer $issueId
	 */
	public function actionAddEntry($issueId) {
		$issue = $this->findModel($issueId);
		$model = new IssueProduct();
		$model->issue_id = $issue->id;
		
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
	 * Updates an existing IssueProduct model.
	 * @param integer $issueId
	 * @param integer $productId
	 */
	public function actionUpdateEntry($issueId, $productId) {
		$model = $this->findIssueProductModel($issueId, $productId);
		
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
     * Deletes an existing IssueProduct model.
     * If deletion is successful, the browser will be redirected to the 'view' page.
     * @param integer $issueId
	 * @param integer $productId
     * @return mixed
     */
    public function actionDeleteEntry($issueId, $productId)
    {
        $this->findIssueProductModel($issueId, $productId)->delete();
		Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => true];
    }

	/**
	 * Adds a comment to an existing Issue.
	 * @param integer $issueId
	 */
	public function actionAddComment($issueId) {
		$issue = $this->findModel($issueId);
		$model = new IssueComment();
		$model->issue_id = $issue->id;
		
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        } else {
            return $this->renderAjax('add-comment', [
                'model' => $model,
            ]);
        }
	}
	
	/**
	 * Gets the envelope for a given issue.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionGetEnvelope($id) {
		$model = $this->findModel($id);
		$content = $this->renderPartial('envelope', ['model' => $model]);

		// setup kartik\mpdf\Pdf component
		$pdf = new Pdf([
			'filename' => $model->id . '.pdf',
			'mode' => Pdf::MODE_UTF8,
			'format' => [100, 50],
			'marginLeft' => 0,
			'marginBottom' => 0,
			'marginTop' => 0,
			'marginRight' => 0,
			'destination' => Pdf::DEST_DOWNLOAD,
			'content' => $content,
			'cssFile' => '@webroot/css/envelope.css',
		]);

		// return the pdf output as per the destination setting
		return $pdf->render();
	}
	
    /**
     * Finds the Issue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Issue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if ($this->model || ($this->model = Issue::findOne($id)) !== null) {
            return $this->model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	/**
     * Finds the IssueProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $issueId
	 * @param integer $productId
     * @return IssueProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findIssueProductModel($issueId, $productId)
    {
        if (($model = IssueProduct::findOne(['issue_id' => $issueId, 'product_id' => $productId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
