<?php

namespace app\controllers;

class DocsController extends \yii\web\Controller
{

  /**
  * @inheritdoc
  */
  public function actions() {
    return [
      'view' => [
        'class' => 'yii\web\ViewAction',
        'viewPrefix' => null,
      ],
    ];
  }
  
}
