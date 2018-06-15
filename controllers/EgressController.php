<?php

namespace app\controllers;

use Yii;
use app\filters\AuthorRule;
use app\models\Egress;
use app\models\EgressProduct;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * EgressController implements the CRUD actions for Egress model.
 */
class EgressController extends Controller
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
      							return $this->findModel(Yii::$app->request->getQueryParam('egressId'));
      						},
      						'roles' => ['admin', 'author'],
      					],
      					[
      						'actions' => ['index', 'view', 'create'],
      						'roles' => ['@'],
      					],
      				],
      			],
        ];
    }

    /**
     * Lists all Egress models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Egress::find()->with(['user']),
            'sort' => [
              'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Egress model.
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
     * Creates a new Egress model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Egress();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Egress model.
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
     * Deletes an existing Egress model.
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
  	 * Adds a product to an existing Egress.
  	 * @param integer $egressId
  	 */
  	public function actionAddEntry($egressId) {
  		$egress = $this->findModel($egressId);
  		$model = new EgressProduct();
  		$model->egressId = $egress->id;

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
  	 * Updates an existing EgressProduct model.
  	 * @param integer $egressId
  	 * @param integer $productId
  	 */
  	public function actionUpdateEntry($egressId, $productId) {
  		$model = $this->findEgressProductModel($egressId, $productId);

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
  	 * Deletes an existing EgressProduct model.
  	 * @param integer $egressId
  	 * @param integer $productId
  	 * @return mixed
  	 */
  	public function actionDeleteEntry($egressId, $productId)
  	{
  		$this->findEgressProductModel($egressId, $productId)->delete();
  		Yii::$app->response->format = Response::FORMAT_JSON;
  		return ['success' => true];
  	}

    /**
     * Finds the Egress model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Egress the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
      if ($this->model || ($this->model = Egress::find()->andWhere(['id' => $id])->with('egressProducts.product.model')->one()) !== null) {
        return $this->model;
      } else {
        throw new NotFoundHttpException('The requested page does not exist.');
      }
    }

    /**
  	 * Finds the EgressProduct model.
  	 * If the model is not found, a 404 HTTP exception will be thrown.
  	 * @param integer $egressId
  	 * @param integer $productId
  	 * @return EgressProduct the loaded model
  	 * @throws NotFoundHttpException if the model cannot be found
  	 */
  	protected function findEgressProductModel($egressId, $productId)
  	{
  		if (($model = EgressProduct::findOne(['egress_id' => $egressId, 'product_id' => $productId])) !== null) {
  			return $model;
  		} else {
  			throw new NotFoundHttpException('The requested page does not exist.');
  		}
  	}
}
