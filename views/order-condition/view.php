<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\OrderCondition */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['order/index']];
$this->params['breadcrumbs'][] = ['label' => 'Condiciones de Venta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-condition-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Borrar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro de eliminar este elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            [
                'attribute' => 'interest_rate_percentage',
                'format' => 'percent',
                'value' => isset($model->interest_rate_percentage) ? ($model->interest_rate_percentage / 100) : null,
            ],
            [
                'attribute' => 'editable_in_order',
                'value' => $model->editable_in_order ? 'Sí' : 'No',
            ],
        ],
    ]) ?>

</div>
