<?php

namespace app\controllers;

use app\models\Material;
use app\models\Model;
use app\models\ModelSearch;
use dektrium\user\filters\AccessRule;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ModelController implements the CRUD actions for Model model.
 */
class ModelController extends Controller
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
      						'actions' => ['index', 'view', 'list', 'list-materials'],
      						'allow' => true,
      						'roles' => ['@'],
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
     * Lists all Model models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Model model.
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
     * Creates a new Model model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Model();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Model model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Model model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
  	 * Builds a response for Select2 product widgets
  	 * @param string $q
  	 * @return JSON
  	 */
  	public function actionList($q = '') {
  		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
  		$modelsArray = Model::find()
        ->active()
        ->andWhere(['like', 'name', $q])
        ->orderBy(['name' => SORT_ASC])
        ->asArray()
        ->all();
  		$results = array_map(function ($modelArray) {
  			return ['id' => $modelArray['id'], 'text' => $modelArray['name']];
  		}, $modelsArray);
  		$out = ['results' => $results];
  		return $out;
  	}

    /**
    * Returns a JSON object coitaing materials.
    * @param mixed $query
    * @return mixed JSON response.
    */
    public function actionListMaterials($query) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
  		$materialsArray = Material::find()
        ->andWhere(['like', 'name', $query])
        ->orderBy(['name' => SORT_ASC])
        ->asArray()
        ->all();
  		$results = array_map(function ($materialArray) {
  			return ['name' => $materialArray['name']];
  		}, $materialsArray);
  		return $results;
    }

    /**
     * Finds the Model model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Model the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Model::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
