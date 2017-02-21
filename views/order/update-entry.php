<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProduct */

$this->title = 'Editar Producto: ' . $model->product_id;
?>
<div class="order-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_entry-form', [
        'model' => $model,
    ]) ?>

</div>
