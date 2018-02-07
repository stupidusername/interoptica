<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;

class SiteController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['logout'],
				'rules' => [
					[
						'actions' => ['logout', 'user-list'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	/**
	 * Displays homepage.
	 *
	 * @return string
	 */
	public function actionIndex() {
		if (Yii::$app->user->can('management')) {
			return $this->redirect(['order/statistics']);
		}
		return $this->render('index');
	}

	/**
	 * Builds a response for Select2 user widgets
	 * @param string $q
	 * @return JSON
	 */
	public function actionUserList($q = '') {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$usersArray = User::find()->active()->andWhere(
				['or', ['gecom_id' => $q], ['like', 'username', $q], ['like', 'email', $q], ['like', 'name', $q], ['like', 'public_email', $q]])
				->joinWith(['profile'])->asArray()->all();
		$results = array_map(function ($userArray) {
					return ['id' => $userArray['id'], 'text' => $userArray['username']];
				}, $usersArray);
		$out = ['results' => $results];
		return $out;
	}
}
