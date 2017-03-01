<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\widgets\modal\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\OrderAsset;
use app\models\OrderStatus;
use app\models\Order;

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

OrderAsset::register($this);
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
		<?= Html::a('Exportar PDF', ['export-pdf', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
				'value' => $model->customer->name,
			],
			[
				'label' => 'Estado',
				'value' => $model->orderStatus->statusLabel,
			],
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
	
	<h3>Pedidos del mismo Cliente</h3>
	
	<?php $statusIds = ArrayHelper::getColumn(OrderStatus::find()->select(['id' => 'max(id)'])->asArray()->groupBy('order_id')->all(), 'id'); ?>
	
	<?php Pjax::begin(['id' => 'pendingOrdersGridview']) ?>
	<?=
	GridView::widget([
		'columns' => [
            'id',
			[
				'label' => 'Estado',
				'value' => 'orderStatus.statusLabel',
			],
            [
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}',
			],
        ],
		'dataProvider' => new ActiveDataProvider([
            'query' => Order::find()->andWhere(['and', ['!=', 'order.id', $model->id], ['=', 'customer_id', $model->customer_id]])
			->innerJoinWith(['orderStatus' => function ($query) use ($statusIds) {
				$query->andWhere(['and', ['in', 'order_status.id', $statusIds], ['not in', 'status', [OrderStatus::STATUS_SENT, OrderStatus::STATUS_DELIVERED]]]);
				
			}])->orderBy('order.id'),
			'pagination' => false,
			'sort' => false,
        ]),
	]);
	?>
	<?php Pjax::end() ?>

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
	
	<p>
		<?= Html::button('Agregar Producto', ['class' => 'btn btn-success', 'onclick' => "$('#addEntry').kbModalAjax({url: '$addEntryUrl'}); $('#addEntry').modal('show');"]) ?>
	</p>
	
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
