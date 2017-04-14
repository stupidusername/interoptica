<?php

namespace app\controllers;

use Yii;
use app\models\Issue;
use app\models\IssueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\filters\AuthorRule;

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
                        'actions' => ['view'],
						'verbs' => ['POST'],
						'model' => function() {
							return $this->findModel(Yii::$app->request->getQueryParam('id'));
						},
						'roles' => ['admin', 'author'],
                    ],
                    [
						'actions' => ['index', 'view'],
						'verbs' => ['GET'],
                        'roles' => ['@'],
                    ],
					[
						'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                ],
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Issue model.
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
     * Creates a new Issue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Issue();

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
}
