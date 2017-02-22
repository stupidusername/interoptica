<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\widgets\modal\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\OrderEntryAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$addEntryUrl = Url::to(['add-entry', 'orderId' => $model->id]);

Modal::begin([
    'id' => 'addEntry',
    'url' => $addEntryUrl,
]);

Modal::end(); 

OrderEntryAsset::register($this);
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
		<?= Html::button('Agregar Producto', ['class' => 'btn btn-success', 'onclick' => "$('#addEntry').kbModalAjax({url: '$addEntryUrl'}); $('#addEntry').modal('show');"]) ?>
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
			'sort' => false,
        ]),
	]);
	?>
	
	<h3>Productos</h3>
	
	<?php Pjax::begin(['id' => 'productsGridview']); ?>
	<?=
	GridView::widget([
		'columns' => [
			'product.gecom_desc',
			[
				'attribute' => 'price',
				'format' => 'currency',
			],
			'quantity',
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete}',
				'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
					switch ($action) {
						case 'update':
							return Url::to(['update-entry', 'orderId' => $model->order_id, 'productId' => $model->product_id]);
						case 'delete':
							return Url::to(['delete-entry', 'orderId' => $model->order_id, 'productId' => $model->product_id]);
					}
				},
				'buttons' => [
					'update' => function ($url, $model, $key) {
						return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]), $url, ['class' => 'productUpdate']);
					},
					'delete' => function ($url, $model, $key) {
						return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'productDelete']);
					},
				],
			],
		],
		'dataProvider' => new ActiveDataProvider([
            'query' => $model->getOrderProducts()->with(['product']),
			'pagination' => false,
			'sort' => false,
        ]),
	]);
	?>
	<?php Pjax::end(); ?>
	
</div>
