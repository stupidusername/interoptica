<?php

namespace app\controllers;

use yii\web\Controller;

/**
 * ImageControll
 */
class ImageController extends Controller
{
  /**
  * @inheritdoc
  */
  public function actions() {
    return [
      'thumb' => 'iutbay\yii2imagecache\ThumbAction',
    ];
  }
}
