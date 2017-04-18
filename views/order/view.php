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
use app\models\IssueStatus;
use app\models\Issue;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Pedido: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$addEntryUrl = Url::to(['add-entry', 'orderId' => $model->id]);
$statusIds = ArrayHelper::getColumn(OrderStatus::getLastStatuses()->all(), 'id');
$clientPendingOrdersQuery = Order::find()->andWhere(['and', ['!=', 'order.id', $model->id], ['=', 'customer_id', $model->customer_id]])
		->innerJoinWith(['orderStatus' => function ($query) use ($statusIds) {
			$query->andWhere(['and', ['in', 'order_status.id', $statusIds], ['not in', 'status', [OrderStatus::STATUS_SENT, OrderStatus::STATUS_DELIVERED]]]);
		}])->orderBy('order.id');
$clientPendingOrders = $clientPendingOrdersQuery->all();

$statusIds = ArrayHelper::getColumn(IssueStatus::getLastStatuses()->all(), 'id');
$clientPendingIssuesQuery = Issue::find()->andWhere(['and', ['!=', 'issue.id', $model->id], ['=', 'customer_id', $model->customer_id]])
		->innerJoinWith(['issueStatus' => function ($query) use ($statusIds) {
			$query->andWhere(['and', ['in', 'issue_status.id', $statusIds], ['not in', 'status', [IssueStatus::STATUS_CLOSED]]]);
		}])->orderBy('issue.id');
$clientPendingIssues = $clientPendingIssuesQuery->all();

Modal::begin([
	'id' => 'addEntry',
	'url' => $addEntryUrl,
	'options' => [
		'tabindex' => false // important for Select2 to work properly
	],
]);

Modal::end(); 

OrderAsset::register($this);
?>

<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php Pjax::begin(['id' => 'orderSummary']) ?>
	<?php if (count($clientPendingOrders) || count($clientPendingIssues)): ?>
		<div class="error-summary">
			<?php if (count($clientPendingOrders)): ?>
				<h4>El cliente tiene <?= count($clientPendingOrders) ?> pedido(s) en proceso.</h4>
			<?php elseif (count($clientPendingIssues)): ?>
				<h4>El cliente tiene <?= count($clientPendingIssues) ?> reclamo(s) en proceso.</h4>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php Pjax::end() ?>
	
    <p>
		<?= Html::button('Mostrar/Ocultar Detalle', ['class' => 'btn btn-primary', 'onclick' => '$("#orderDetail").toggle()']) ?>
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
		<?= Html::a('Crear Reclamo', ['/issue/create', 'orderId' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

	<div id="orderDetail" style="display: none;">
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
					'format' => 'raw',
					'value' => Editable::widget([
						'inputType' => Editable::INPUT_DROPDOWN_LIST,
						'model' => $model,
						'attribute' => 'status',
						'data' => OrderStatus::statusLabels(),
						'displayValue' => $model->orderStatus->statusLabel,
						'pluginEvents' => [
							'editableSuccess' => 'function () { $.pjax.reload({container: "#orderStatusGridview"}); }',
						],
					]),
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

		<?php Pjax::begin(['id' => 'pendingGridview']) ?>
		<h3>Pedidos del mismo Cliente</h3>
		
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
				'query' => $clientPendingOrdersQuery,
				'pagination' => false,
				'sort' => false,
			]),
		]);
		?>
		
		<h3>Reclamos del mismo Cliente</h3>
		
		<?=
		GridView::widget([
			'columns' => [
				'id',
				[
					'label' => 'Estado',
					'value' => 'issueStatus.statusLabel',
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => '{view}',
					'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
						switch ($action) {
							case 'view':
								return Url::to(['/issue/view', 'id' => $model->id]);
						}
					},
				],
			],
			'dataProvider' => new ActiveDataProvider([
				'query' => $clientPendingIssuesQuery,
				'pagination' => false,
				'sort' => false,
			]),
		]);
		?>
		<?php Pjax::end() ?>

		<h3>Seguimiento de Estados</h3>

		<?php Pjax::begin(['id' => 'orderStatusGridview']) ?>
		<?=
		GridView::widget([
			'columns' => [
				[
					'attribute' => 'status',
					'value' => 'statusLabel',
				],
				[
					'label' => 'Usuario',
					'attribute' => 'user.username'
				],
				[
					'attribute' => 'create_datetime',
					'format' => 'datetime'
				],
			],
			'dataProvider' => new ActiveDataProvider([
				'query' => $model->getOrderStatuses()->with(['user']),
				'pagination' => false,
				'sort' => false,
			]),
		]);
		?>
		<?php Pjax::end() ?>
	</div>
	
	<h3>Productos</h3>
	
	<p>
		<?= Html::button('Agregar Producto', ['id' => 'addEntryButton', 'class' => 'btn btn-success', 'url' => "$addEntryUrl"]) ?>
	</p>
	
	<?php Pjax::begin(['id' => 'productsGridview']); ?>
	<?=
	GridView::widget([
		'summary' => 'Totales: <b>{totalCount}</b> productos. <b>' . $model->totalQuantity . '</b> piezas. <b>' . Yii::$app->formatter->asCurrency($model->subtotal) . '</b>.',
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
            'query' => $model->getOrderProducts()->with(['product'])->orderBy(['id' => SORT_DESC]),
			'pagination' => false,
			'sort' => false,
        ]),
	]);
	?>
	<?php Pjax::end(); ?>
	
</div>
