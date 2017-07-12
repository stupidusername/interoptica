<?php

namespace app\controllers;

use Yii;
use app\models\MonthlySummary;
use app\models\MonthlySummarySearch;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * MonthlySummaryController implements the CRUD actions for MonthlySummary model.
 */
class MonthlySummaryController extends Controller
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
						'allow' => true,
						'roles' => ['admin'],
					],
				],
			],
		];
	}

	/**
	 * Lists all MonthlySummary models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new MonthlySummarySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}
