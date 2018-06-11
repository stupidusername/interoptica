<?php

namespace app\controllers;

use Yii;
use app\models\Batch;
use app\models\BatchSearch;
use dektrium\user\filters\AccessRule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * BatchController implements the CRUD actions for Batch model.
 */
class BatchController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
      return [
        'access' => [
          'class' => AccessControl::className(),
          'ruleConfig' => [
            'class' => AccessRule::className(),
          ],
          'rules' => [
            [
              'actions' => ['index', 'view'],
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
     * Lists all Batch models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BatchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Batch model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Batch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $models = [new Batch(['scenario' => Batch::SCENARIO_CREATE])];
        $request = Yii::$app->getRequest();
        if ($request->isPost && $request->post('ajax') !== null) {
            $data = Yii::$app->request->post('Batch', []);
            foreach (array_keys($data) as $index) {
                $models[$index] = new Batch(['scenario' => Batch::SCENARIO_CREATE]);
            }
            Batch::loadMultiple($models, Yii::$app->request->post());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $result = Batch::validateMultiple($models);
            return $result;
        }

        if ($request->isPost) {
          $data = Yii::$app->request->post('Batch', []);
          foreach (array_keys($data) as $index) {
              $models[$index] = new Batch(['scenario' => Batch::SCENARIO_CREATE]);
          }
          if (Batch::loadMultiple($models, Yii::$app->request->post()) && Batch::validateMultiple($models)) {
              foreach ($models as $model) {
                $model->save();
              }
              $this->redirect(['index']);
          }
        }

        return $this->render('create', ['models' => $models]);
    }

    /**
     * Updates an existing Batch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Batch::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Batch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Batch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Batch::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
