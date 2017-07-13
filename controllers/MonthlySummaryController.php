<?php

namespace app\controllers;

use app\models\MonthlySummary;
use app\models\MonthlySummarySearch;
use dektrium\user\filters\AccessRule;
use kartik\grid\EditableColumnAction;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
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
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'edit' => [
				'class' => EditableColumnAction::className(),
				'modelClass' => MonthlySummary::className(),
			],
		]);
	}

	/**
	 * Lists all MonthlySummary models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		MonthlySummary::createModels();

		$searchModel = new MonthlySummarySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}
