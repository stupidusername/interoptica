<?php

namespace app\controllers\api;

use app\models\Suitcase;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;

class SuitcaseController extends BaseController {

    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'roles' => ['admin', 'api_client'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function actionList() {
        $query = Suitcase::find()->active()->with(['suitcaseBrands.brand']);
        $models = $query->asArray()->all();
        $suitcases = [];
        foreach ($models as $model) {
          $brands = array_map(function($suitcaseBrand) {
              return [
                'id' => $suitcaseBrand['brand']['id'],
                'name' => $suitcaseBrand['brand']['name'],
              ];
          }, $model['suitcaseBrands']);
          $suitcases[] = [
              'id' => (int) $model['id'],
              'name' => $model['name'],
              'brands' => $brands,
          ];
        }
        return $suitcases;
    }
}
