<?php

namespace app\controllers;

use app\models\ApiKey;
use Yii;

class ApiController extends \yii\rest\Controller {

  /**
  *  @inheritdoc
  */
  public function beforeAction($action) {
    if (!parent::beforeAction($action)) {
      return false;
    }
    $key = Yii::$app->request->getQueryParam('key');
    if ($key && ApiKey::findOne(['key' => $key])) {
      return true;
    } else {
        throw new \yii\web\UnauthorizedHttpException;
    }
  }

  public function actionGetOrders($updated_since=null) {
    return [];
  }

}
