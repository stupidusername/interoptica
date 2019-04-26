<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrderCondition */

$this->title = 'Editar Condición de Venta: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['order/index']];
$this->params['breadcrumbs'][] = ['label' => 'Condiciones de Venta', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="order-condition-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
