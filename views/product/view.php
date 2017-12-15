<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->gecom_desc;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

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
            'gecom_code',
            'gecom_desc',
            [
				'attribute' => 'price',
				'format' => 'currency',
			],
            'stock',
            [
      		    'attribute' => 'running_low',
      		    'value' => $model->running_low ? 'Sí' : 'No',
      	    ],
            [
              'attribute' => 'running_low_date',
              'format' => 'date',
            ],
	    [
		    'attribute' => 'extra',
		    'value' => $model->extra ? 'Sí' : 'No',
	    ],
        ],
    ]) ?>

</div>
