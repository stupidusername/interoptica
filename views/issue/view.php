<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Issue */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-view">

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
            'id',
            [
				'label' => 'Usuario',
				'value' => $model->user->username,
			],
            [
				'label' => 'Cliente',
				'value' => $model->customer_id ? $model->customer->name : null,
			],
            'order_id',
            [
				'label' => 'Producto',
				'value' => $model->product_id ? $model->product->gecom_desc : null,
			],
            [
				'label' => 'Tipo',
				'value' => $model->issueType->name,
			],
            'comment:ntext',
            'contact',
        ],
    ]) ?>

</div>
