<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

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
		<?= Html::a('Exportar TXT', ['export-txt', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'customer_id',
            'discount_percentage',
            'comment:ntext',
            'deleted',
        ],
    ]) ?>

	<h3>Seguimiento de Estados</h3>
	
	<?=
	GridView::widget([
		'columns' => [
			[
				'attribute' => 'status',
				'value' => 'statusLabel',
			],
			'create_datetime'
		],
		'dataProvider' => new ActiveDataProvider([
            'query' => $model->getOrderStatuses(),
			'pagination' => false,
        ]),
	]);
	?>
	
</div>
