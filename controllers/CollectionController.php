<?php

namespace app\controllers;

use Yii;
use app\models\Collection;
use app\models\CollectionProduct;
use app\models\ProductSearch;
use dektrium\user\filters\AccessRule;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * CollectionController implements the CRUD actions for Collection model.
 */
class CollectionController extends Controller
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
      						'actions' => ['index', 'view'],
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
     * Lists all Collection models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Collection::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Collection model.
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
     * Creates a new Collection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Collection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Collection model.
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
     * Deletes an existing Collection model.
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
  	 * Adds a product to an existing Collection.
  	 * @param integer $collectionId
     * @return mixed
  	 */
  	public function actionAddEntry($collectionId) {
  		$collection = $this->findModel($collectionId);
  		$model = new CollectionProduct();
  		$model->collection_id = $collection->id;

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
     * Renders a view that allows to edit the products of a collection
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEditProducts($id)
    {
      $model = $this->findModel($id);

      $searchModel = new ProductSearch();
  		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

  		return $this->render('edit-products', [
        'model' => $model,
  			'searchModel' => $searchModel,
  			'dataProvider' => $dataProvider,
  		]);
    }

    /**
     * Adds a product to an existing Collection.
     * @param integer $collectionId
     * @param integer $productId
     */
    public function actionCheckEntry($collectionId, $productId) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $model = new CollectionProduct();
      $model->collection_id = $collectionId;
      $model->product_id = $productId;
      if ($model->save()) {
        $response = ['success' => true];
      } else {
        $response = ['success' => false];
      }
      return $response;
    }

    /**
  	 * Deletes an existing CollectionProduct model.
  	 * @param integer $collectionId
  	 * @param integer $productId
  	 * @return mixed
  	 */
  	public function actionDeleteEntry($collectionId, $productId)
  	{
  		$this->findCollectionProductModel($collectionId, $productId)->delete();
  		Yii::$app->response->format = Response::FORMAT_JSON;
  		return ['success' => true];
  	}

    /**
     * Finds the Collection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Collection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Collection::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
  	 * Finds the CollectionProduct model based on its primary key value.
  	 * If the model is not found, a 404 HTTP exception will be thrown.
  	 * @param integer $collectionId
  	 * @param integer $productId
  	 * @return CollectionProduct the loaded model
  	 * @throws NotFoundHttpException if the model cannot be found
  	 */
  	protected function findCollectionProductModel($collectionId, $productId)
  	{
  		if (($model = CollectionProduct::findOne(['collection_id' => $collectionId, 'product_id' => $productId])) !== null) {
  			return $model;
  		} else {
  			throw new NotFoundHttpException('The requested page does not exist.');
  		}
  	}
}
