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
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Pedido: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$addEntryUrl = Url::to(['add-entry', 'orderId' => $model->id]);
$clientPendingOrdersQuery = Order::find()->andWhere(['and', ['!=', 'order.id', $model->id], ['=', 'customer_id', $model->customer_id]])
		->innerJoinWith(['orderStatus' => function ($query) {
			$query->andWhere(['<', 'status', OrderStatus::STATUS_SENT]);
		}])->orderBy('order.id');
$clientPendingOrders = $clientPendingOrdersQuery->all();

$clientPendingIssuesQuery = Issue::find()->andWhere(['customer_id' => $model->customer_id])
		->innerJoinWith(['issueType', 'issueStatus' => function ($query) {
			$query->andWhere(['<', 'status', IssueStatus::STATUS_CLOSED]);
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

Modal::begin([
	'id' => 'addInvoice',
	'url' => Url::to(['add-invoice', 'orderId' => $model->id]),
]);

Modal::end();

OrderAsset::register($this);
?>

<div class="order-view">

  <h1>
		<?= Html::encode($this->title) ?>
	</h1>

	<?php Pjax::begin(['id' => 'orderSummary']) ?>
	<?php if (count($clientPendingOrders) || count($clientPendingIssues)): ?>
		<div class="error-summary">
			<?php if (count($clientPendingOrders)): ?>
				<h4>El cliente tiene <?= count($clientPendingOrders) ?> pedido(s) en proceso.</h4>
			<?php endif; ?>
			<?php if (count($clientPendingIssues)): ?>
				<h4>El cliente tiene <?= count($clientPendingIssues) ?> reclamo(s) en proceso.</h4>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php Pjax::end() ?>

    <p>
		<?php if ($model->status == OrderStatus::STATUS_LOADING): ?>
			<?= Html::a('Ingresar Pedido', ['enter', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
		<?php endif; ?>
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
		<?= Html::a('Enviar Email', ['send-email', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
					'value' => $model->customer->displayName,
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
							'editableSuccess' => 'function () { $.pjax.reload({container: "#orderStatusGridview", timeout: 30000}); }',
						],
					]),
				],
				[
					'label' => 'Transporte',
					'value' => $model->transport ? $model->transport->name : '',
				],
				[
					'attribute' => 'iva',
					'format' => 'percent',
					'value' => function ($model) {
						return $model->iva / 100;
					},
				],
				[
					'attribute' => 'discount_percentage',
					'format' => 'percent',
					'value' => function ($model) {
						return $model->discount_percentage / 100;
					},
				],
				[
					'label' => 'Condición de Venta',
					'attribute' => 'orderCondition.title',
				],
				[
					'attribute' => 'interest_rate_percentage',
					'format' => 'percent',
					'value' => function ($model) {
						return $model->interest_rate_percentage / 100;
					},
				],
				'comment:ntext',
			],
		]) ?>

		<h3>Dirección de Entrega</h3>

		<?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				[
					'attribute' => 'delivery_address',
					'label' => 'Calle, Altura, Departamento',
				],
				[
					'attribute' => 'delivery_city',
				],
				[
					'attribute' => 'delivery_state',
				],
				[
					'attribute' => 'delivery_zip_code',
				],
			],
		]) ?>

		<h3>Facturas</h3>
		<p>
			<?= Html::button('Agregar Factura', ['id' => 'addInvoiceButton', 'class' => 'btn btn-success']) ?>
		</p>
		<?php Pjax::begin(['id' => 'invoicesGridview']) ?>
		<?=
		GridView::widget([
			'columns' => [
				'number',
				'comment:ntext',
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => '{delete}',
					'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
						switch ($action) {
							case 'delete':
								return Url::to(['delete-invoice', 'orderId' => $model->order_id, 'invoiceId' => $model->id]);
						}
					},
					'buttons' => [
						'delete' => function ($url, $model, $key) {
							return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'invoiceDelete']);
						},
					],
				],
			],
			'dataProvider' => new ActiveDataProvider([
				'query' => $model->getOrderInvoices(),
				'pagination' => false,
				'sort' => false,
			]),
		]);
		?>
		<?php Pjax::end() ?>

		<?php Pjax::begin(['id' => 'pendingGridview']) ?>
		<h3>Pedidos del mismo Cliente (<?= count($clientPendingOrders) ?>)</h3>

		<p>
			<?= Html::button('Mostrar/Ocultar', ['class' => 'btn btn-primary', 'onclick' => '$("#pendingOrders").toggle()']) ?>
		</p>

		<?=
		GridView::widget([
			'options' => [
				'id' => 'pendingOrders',
				'style' => ['display' => 'none'],
			],
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

		<h3>Reclamos del mismo Cliente (<?= count($clientPendingIssues) ?>)</h3>

		<p>
			<?= Html::button('Mostrar/Ocultar', ['class' => 'btn btn-primary', 'onclick' => '$("#pendingIssues").toggle()']) ?>
		</p>

		<?=
		GridView::widget([
			'options' => [
				'id' => 'pendingIssues',
				'style' => ['display' => 'none'],
			],
			'columns' => [
				'id',
				[
					'label' => 'Tipo',
					'value' => 'issueType.name',
				],
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
		<?= Html::button('Agregar Productos', ['id' => 'addEntryButton', 'class' => 'btn btn-success', 'url' => "$addEntryUrl"]) ?>
	</p>

	<?php Pjax::begin(['id' => 'productsGridview']); ?>
	<?=
	GridView::widget([
		'summary' =>
			'Productos: <b>{totalCount}</b>.' .
			' Piezas: <b>' . $model->totalQuantity . '</b>.' .
			' Subtotal: <b>' . Yii::$app->formatter->asCurrency($model->subtotal) . '</b>.' .
			' Subtotal + IVA: <b>' . Yii::$app->formatter->asCurrency($model->subtotalPlusIva) . '</b>.' .
			' Financiación: <b>' . Yii::$app->formatter->asCurrency($model->financing) . '</b>.' .
			' Total: <b>' . Yii::$app->formatter->asCurrency($model->total) . '</b>.',
		'rowOptions' => function ($model, $index, $widget, $grid) {
			return $model->ignore_stock ? ['style'=>"font-weight: bold;"] : [];
		},
		'columns' => [
			'product.code',
			[
				'attribute' => 'price',
				'format' => 'currency',
			],
			'quantity',
			[
				'attribute' => 'ignore_stock',
				'value' => function ($model) {
					return $model->ignore_stock ? 'Sí' : 'No';
				},
			],
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
            'query' => $model->getOrderProducts()->innerJoinWith(['product'])->orderBy(['product.code' => SORT_ASC]),
			'pagination' => false,
			'sort' => false,
        ]),
	]);
	?>

	<?=
	ExportMenu::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getOrderProducts()->joinWith(['product.model.brand'])->orderBy(['product.code' => SORT_ASC]),
			'pagination' => false,
			'sort' => false,
		]),
		'target' => ExportMenu::TARGET_SELF,
		'showConfirmAlert' => false,
		'filename' => 'productos-pedido-'. $model->id,
		'columns' => [
			'product.code',
			[
				'attribute' => 'product.model.brand.name',
				'label' => 'Marca',
			],
			[
				'attribute' => 'product.model.typeLabel',
				'label' => 'Tipo',
			],
			[
				'attribute' => 'product.model.name',
				'label' => 'Modelo',
			],
			[
				'attribute' => 'product.model.description',
				'format' => 'ntext',
			],
			'product.model.front_size',
			'product.model.lens_width',
			'product.model.bridge_size',
			'product.model.temple_length',
			'product.model.base',
			[
				'attribute' => 'product.model.flex',
				'value' => function($model) {
					return $model->product->model->flex ? 'Sí' : 'No';
				}
			],
			[
				'attribute' => 'product.polarized',
				'value' => function($model) {
					return $model->product->polarized ? 'Sí' : 'No';
				}
			],
			[
				'attribute' => 'product.mirrored',
				'value' => function($model) {
					return $model->product->mirrored ? 'Sí' : 'No';
				}
			],
			[
				'attribute' => 'price',
				'format' => 'currency',
			],
			'quantity',
		],
	]);
	?>

	<?php Pjax::end(); ?>

</div>
