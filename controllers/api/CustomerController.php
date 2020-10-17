<?php

namespace app\controllers\api;

use app\models\api\Pagination;
use app\models\api\PaginatedItems;
use app\models\Customer;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;
use yii\helpers\Url;

class CustomerController extends BaseController {

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

    public function actionList($updated_since = null, int $page = 1, int $pagelen = 100) {
        // Validate params.
        if ($page < 1) {
            throw new BadRequestHttpException('Page cannot be lower than 1.');
        }
        if ($pagelen < 1 || $pagelen > 100) {
            throw new BadRequestHttpException('The specified pagelen must take a value between 1 and 100.');
        }
        // Validate date format.
      if ($updated_since && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $updated_since)) {
        throw new BadRequestHttpException('updated_since must be of the format yyyy-mm-dd');
    }
        // Build query.
        $where = [];
        if ($updated_since) {
          $where = ['or', ['>=', 'create_datetime', $updated_since], ['>=', 'update_datetime', $updated_since]];
        }
        $query = Customer::find()->where($where)->limit($pagelen)->offset(($page - 1) * $pagelen);
        // Get items.
        $customers = [];
        $models = $query->asArray()->all();
        foreach ($models as $model) {
          $customers[] = [
              'id' => (int) $model['id'],
              'gecom_id' => (int) $model['gecom_id'],
              'name' => $model['name'],
              'email' => $model['email'],
              'cuit' => $model['cuit'],
              'tax_situation' => $model['tax_situation'],
              'tax_situation_category' => $model['tax_situation_category'],
              'address' => $model['address'],
              'zip_code' => $model['zip_code'],
              'province' => $model['province'],
              'locality' => $model['locality'],
              'phone_number' => $model['phone_number'],
              'deleted' => (bool) $model['deleted'],
            ];
        }
        // Build pagination.
        $totalItems = (integer) $query->count();
        $totalPages = ceil($totalItems / $pagelen);
        $nextPage = $page < $totalPages ? Url::to(['list', 'updated_since' => $updated_since, 'page' => $page + 1, 'pagelen' => $pagelen], true) : null;
        $prevPage = $page > 1 ? Url::to(['list', 'updated_since' => $updated_since, 'page' => $page - 1, 'pagelen' => $pagelen], true) : null;
        $pagination = new Pagination([
            'next' => $nextPage,
            'prev' => $prevPage,
            'pagelen' => $pagelen,
            'page' => $page,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ]);
        $paginatedItems = new PaginatedItems([
            'pagination' => $pagination,
            'items' => $customers,
        ]);
        return $paginatedItems;
    }
}
