<?php

namespace app\controllers\api;

use app\models\User;
use dektrium\user\helpers\Password;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\Controller;

class BaseController extends Controller {

    public function init() {
        parent::init();
        // When enableSession is false, the user authentication status will NOT
        // be persisted across requests using sessions.
        Yii::$app->user->enableSession = false;
        // Set the loginUrl property to be null to show a HTTP 403 error
        // instead of redirecting to the login page.
        Yii::$app->user->loginUrl = null;
    }

    public function behaviors() {
        $behaviors = [
            'basicAuth' => [
                'class' => HttpBasicAuth::className(),
                'auth' => function ($username, $password) {
                    $user = User::find()->active()->where(['username' => $username])->one();
                    if ($user && Password::validate($password, $user->password_hash)
                            && $user->isConfirmed && !$user->isBlocked) {
                        return $user;
                    }
                    return null;
                },
            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }
}
