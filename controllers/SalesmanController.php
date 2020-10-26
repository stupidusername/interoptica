<?php

namespace app\controllers;

use app\models\SalesmanSuitcase;
use app\models\User;
use dektrium\user\filters\AccessRule;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class SalesmanController extends Controller {

    /**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'ruleConfig' => [
					'class' => AccessRule::className(),
				],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['admin'],
					],
				],
			],
		];
	}

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionView($id) {
        $model = $this->findModel($id);
        return $this->render('view', ['model' => $model]);
    }

    public function actionAddSuitcase($id) {
        $user = $this->findModel($id);
		$model = new SalesmanSuitcase();
		$model->user_id = $user->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ['success' => true];
		} else {
			return $this->renderAjax('add-suitcase', [
				'model' => $model,
			]);
		}
    }

    public function actionRemoveSuitcase($id) {
        SalesmanSuitcase::deleteAll(['id' => $id]);
		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['success' => true];
    }

    /**
	 * Finds the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return User the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = User::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}

 ?>
