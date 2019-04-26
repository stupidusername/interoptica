<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrderCondition */

$this->title = 'Crear Condición de Venta';
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['order/index']];
$this->params['breadcrumbs'][] = ['label' => 'Condiciones de Venta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-condition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
