<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\CustomerSearch;
use app\models\CustomersImportForm;
use app\models\IssueSearch;
use app\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use dektrium\user\filters\AccessRule;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
{
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
                    'class' => AccessRule::className(),
                ],
                'rules' => [
					[
						'actions' => ['index', 'create', 'view', 'list'],
						'roles' => ['@'],
						'allow' => true,
					],
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
     * Displays a single Customer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$model = $this->findModel($id);

		$orderSearchModel = new OrderSearch();
		$orderSearchModel->customer_id = $model->id;
		$orderDataProvider = $orderSearchModel->search(Yii::$app->request->queryParams);
		$orderDataProvider->sort->defaultOrder = ['id' => SORT_DESC];
		$orderDataProvider->pagination->pageSize = 5;

		$issueSearchModel = new IssueSearch();
		$issueSearchModel->customer_id = $model->id;
		$issueDataProvider = $issueSearchModel->search(Yii::$app->request->queryParams);
		$issueDataProvider->sort->defaultOrder = ['id' => SORT_DESC];
		$issueDataProvider->pagination->pageSize = 5;

        return $this->render('view', [
            'model' => $model,
			'orderSearchModel' => $orderSearchModel,
			'orderDataProvider' => $orderDataProvider,
			'issueSearchModel' => $issueSearchModel,
			'issueDataProvider' => $issueDataProvider,
        ]);
    }

    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer(['scenario' => Customer::SCENARIO_CREATE]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Customer model.
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
     * Deletes an existing Customer model.
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
	 * Renders import customers page.
	 * @return mixed
	 */
	public function actionImport() {
		$model = new CustomersImportForm();

		if (Yii::$app->request->isPost) {
			$model->file = UploadedFile::getInstance($model, 'file');
			if ($model->import()) {
				// file is uploaded successfully
				return $this->redirect(['index']);
			}
		}

		return $this->render('import', ['model' => $model]);
	}

	/**
	 * Builds a response for Select2 customer widgets
	 * @param string $q
	 * @return JSON
	 */
	public function actionList($q = '') {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$customersArray = Customer::find()->andWhere(['or', ['gecom_id' => $q], ['like', 'name', $q]])->asArray()->all();
		$results = array_map(function ($customerArray) {
					return ['id' => $customerArray['id'], 'text' => $customerArray['name'], 'email' => $customerArray['email'], 'discount_percentage' => $customerArray['discount_percentage']];
				}, $customersArray);
		$out = ['results' => $results];
		return $out;
	}

	/**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
