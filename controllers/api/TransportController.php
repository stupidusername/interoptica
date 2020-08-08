<?php

namespace app\controllers\api;

use app\models\api\Pagination;
use app\models\api\PaginatedItems;
use app\models\Transport;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;
use yii\helpers\Url;

class TransportController extends BaseController {

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

    public function actionList(int $page = 1, int $pagelen = 100) {
        // Validate params.
        if ($page < 1) {
            throw new BadRequestHttpException('Page cannot be lower than 1.');
        }
        if ($pagelen < 1 || $pagelen > 100) {
            throw new BadRequestHttpException('The specified pagelen must take a value between 1 and 100.');
        }
        // Build query.
        $query = Transport::find()->active()->select(['id', 'name'])
            ->limit($pagelen)->offset(($page - 1) * $pagelen);
        // Get items.
        $transports = $query->asArray()->all();
        // Build pagination.
        $totalItems = (integer) $query->count();
        $totalPages = ceil($totalItems / $pagelen);
        $nextPage = $page < $totalPages ? Url::to(['list', 'page' => $page + 1, 'pagelen' => $pagelen], true) : null;
        $prevPage = $page > 1 ? Url::to(['list', 'page' => $page - 1, 'pagelen' => $pagelen], true) : null;
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
            'items' => $transports,
        ]);
        return $paginatedItems;
    }
}
