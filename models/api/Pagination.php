<?php

namespace app\models\api;

use yii\base\Model;

class Pagination extends Model {

    public $next;
    public $prev;
    public $pagelen;
    public $page;
    public $totalItems;
    public $totalPages;

    public function fields() {
        return [
            'next',
            'prev',
            'pagelen',
            'page',
            'total_items' => 'totalItems',
            'total_pages' => 'totalPages',
        ];
    }
}
