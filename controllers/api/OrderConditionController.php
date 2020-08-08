<?php

namespace app\controllers\api;

use app\models\api\Pagination;
use app\models\api\PaginatedItems;
use app\models\OrderCondition;
use yii\helpers\Url;
use yii\rest\Controller;

class OrderConditionController extends Controller {

    public function actionList(int $page = 1, int $pagelen = 100) {
        // Validate params.
        if ($page < 1) {
            throw new BadRequestHttpException('Page cannot be lower than 1.');
        }
        if ($pagelen < 1 || $pagelen > 100) {
            throw new BadRequestHttpException('The specified pagelen must take a value between 1 and 100.');
        }
        // Build query.
        $query = OrderCondition::find()->active()->select(['id', 'title', 'interest_rate_percentage'])
            ->limit($pagelen)->offset(($page - 1) * $pagelen);
        // Get items.
        $orderConditions = $query->asArray()->all();
        array_walk($orderConditions, function(&$row) {
            if ($row['interest_rate_percentage']) {
                $row['interest_rate_percentage'] = (float) $value['interest_rate_percentage'];
            }
        });
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
            'items' => $orderConditions,
        ]);
        return $paginatedItems;
    }
}
