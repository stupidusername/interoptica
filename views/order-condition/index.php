<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Condiciones de venta';
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['order/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-condition-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crear Condición de Venta', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'title',
            [
                'attribute' => 'interest_rate_percentage',
                'format' => 'percent',
                'value' => function ($model) {
                    return isset($model->interest_rate_percentage) ? ($model->interest_rate_percentage / 100) : null;
                },
            ],
            [
                'attribute' => 'editable_in_order',
                'value' => function ($model) {
                    return $model->editable_in_order ? 'Sí' : 'No';
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
