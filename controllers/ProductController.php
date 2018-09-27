<?php

namespace app\controllers;

use app\models\Color;
use app\models\Product;
use app\models\ProductSearch;
use app\models\ProductStatistics;
use dektrium\user\filters\AccessRule;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use zxbodya\yii2\galleryManager\GalleryManagerAction;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
						'actions' => ['index', 'view', 'list', 'list-available', 'list-colors'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['statistics'],
						'roles' => ['admin', 'management'],
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
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'gallery' => [
				'class' => GalleryManagerAction::className(),
				// mappings between type names and model classes (should be the same as in behaviour)
				'types' => [
					'product' => Product::className(),
				],
			],
		]);
	}

	/**
	 * Lists all Product models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new ProductSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		// data provider used for export
		$exportDataProvider = $dataProvider;
		if (Yii::$app->request->isPost && Yii::$app->request->getBodyParam('export_type')) {
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
	* Edit product info from index.
	* @return JSON
	*/
	public function actionEdit() {
		$ids = Yii::$app->request->post('ids', []);
		$id = Yii::$app->request->post('editableKey');
		ArrayHelper::removeValue($ids, $id);
		array_push($ids, $id);

		$out = Json::encode(['output' => '', 'message' => '']);
		foreach ($ids as $modelId) {
			$model = Product::findOne($modelId);

			$post = [];
			$posted = current($_POST['Product']);
			$post['Product'] = $posted;

			if ($model->load($post)) {
				$model->save();
				if ($modelId == $id) {
					$output = '';
					$attribute = Yii::$app->request->post('editableAttribute');
					if ($attribute == 'available') {
						$output = $model->available ? 'Sí' : 'No';
					}
					if (count($model->getErrors())) {
						$errors = '<div><ul>';
						foreach ($model->getErrors() as $fieldErrors) {
							foreach($fieldErrors as $error) {
								$errors .= "<li>$error</li>";
							}
						}
						$errors .= '</ul></div>';
					} else {
						$errors = '';
					}
					$out = Json::encode(['output' => $output, 'message' => $errors]);
				}
			}
		}
		// return ajax json encoded response and exit
		return $out;
	}

	/**
	 * Displays a single Product model.
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
	 * Creates a new Product model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Product(['available' => true]);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Product model.
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
	 * Deletes an existing Product model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		if (!Yii::$app->request->isAjax) {
      return $this->redirect(['index']);
    }
	}

	public function actionStatistics() {
		$model = new ProductStatistics();

		$searchModel = new ProductStatistics();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (Yii::$app->request->isPost) {
			$exportDataProvider->pagination->pageSize = 0;
		}

		return $this->render('statistics', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionList($q = '') {
		return $this->list($q, false);
	}

	public function actionListAvailable($q = '') {
		return $this->list($q, true);
	}

	/**
	 * Builds a response for Select2 product widgets
	 * @param string $q
	 * @param boolean $availableOnly
	 * @return JSON
	 */
	private function list($q, $availableOnly) {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$query = Product::find()
			->active()
			->andWhere(['like', 'code', $q])
			->orderBy(['code' => SORT_ASC])
			->asArray();
		if ($availableOnly) {
			$query->andWhere(['available' => true]);
		}
		$productsArray = $query->all();
		$results = array_map(function ($productArray) {
			return ['id' => $productArray['id'], 'text' => $productArray['code'] . ' (' . $productArray['stock']. ')'];
		}, $productsArray);
		$out = ['results' => $results];
		return $out;
	}

	/**
	* Returns a JSON object coitaing colors.
	* @param mixed $query
	* @return mixed JSON response.
	*/
	public function actionListColors($query) {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$colorsArray = Color::find()
			->andWhere(['like', 'name', $query])
			->orderBy(['name' => SORT_ASC])
			->asArray()
			->all();
		$results = array_map(function ($colorArray) {
			return ['name' => $colorArray['name']];
		}, $colorsArray);
		return $results;
	}

	/**
	 * Finds the Product model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Product the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Product::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
