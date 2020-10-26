<?php

namespace app\controllers\api;

use app\models\api\Pagination;
use app\models\api\PaginatedItems;
use app\models\Product;
use dektrium\user\filters\AccessRule;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

/**
 * Products API endpoints.
 */
class ProductController extends BaseController
{

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
        // Get products.
        $query = Product::find()->with(['model.brand.suitcases'])->offset(($page - 1) * $pagelen)->limit($pagelen)->active();
        $products = $query->all();
        $productsArray = ArrayHelper::toArray(
            $products,
            [
                'app\models\Product' => [
                    'code',
                    'price' => function ($product) {
                        return (float) $product->price;
                    },
                    'stock' => function ($product) {
                        return (integer) $product->stock;
                    },
                    'stock_available' => 'stockAvailable',
                    'available' => function ($product) {
                        return (boolean) $product->available;
                    },
                    'in_suitcases' => function ($product) {
                        return ArrayHelper::getColumn($product->model->brand->suitcases, 'id');
                    },
                ],
            ]
        );
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
            'items' => $productsArray,
        ]);
        return $paginatedItems;
    }

}
