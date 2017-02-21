<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\widgets\modal\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
    'id' => 'addEntry',
    'url' => Url::to(['add-entry', 'orderId' => $model->id]),
    'ajaxSubmit' => true,
]);

Modal::end(); 

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
		<?= Html::button('Agregar Producto', ['class' => 'btn btn-success', 'onclick' => '$("#addEntry").modal("show");']) ?>
		<?= Html::a('Exportar TXT', ['export-txt', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'customer_id',
            [
				'attribute' => 'discount_percentage',
				'format' => 'percent',
				'value' => function ($model) {
					return $model->discount_percentage / 100;
				},
			],
            'comment:ntext',
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
			[
				'attribute' => 'create_datetime',
				'format' => 'datetime'
			],
		],
		'dataProvider' => new ActiveDataProvider([
            'query' => $model->getOrderStatuses(),
			'pagination' => false,
        ]),
	]);
	?>
	
</div>
