<?php

use app\assets\DeliveryAsset;
use app\models\DeliveryStatus;
use app\models\Transport;
use app\widgets\modal\Modal;
use kartik\editable\Editable;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Delivery */

$this->title = 'Envio: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Envios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$addOrderUrl = Url::to(['add-order', 'deliveryId' => $model->id]);
$addIssueUrl = Url::to(['add-issue', 'deliveryId' => $model->id]);

Modal::begin([
	'id' => 'addEntry',
	'url' => '',
	'options' => [
		'tabindex' => false // important for Select2 to work properly
	],
]);

Modal::end();

DeliveryAsset::register($this);
?>
<div class="delivery-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>

<?= Html::a('Borrar', ['delete', 'id' => $model->id], [
	'class' => 'btn btn-danger',
	'data' => [
		'confirm' => '¿Está seguro de eliminar este elemento?',
		'method' => 'post',
	],
]) ?>
    </p>

<?php Pjax::begin(['id' => 'deliveryDetail']) ?>
<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'id',
		'user_id',
		[
			'label' => 'Estado',
			'format' => 'raw',
			'value' => Editable::widget([
				'inputType' => Editable::INPUT_DROPDOWN_LIST,
				'model' => $model,
				'attribute' => 'status',
				'formOptions' => [
					'action' => ['edit-status', 'id' => $model->id],
					'enableClientValidation' => false,
				],
				'data' => DeliveryStatus::statusLabelsWithoutError(),
				'displayValue' => DeliveryStatus::statusLabels()[$model->status],
				'pluginEvents' => [
					'editableSuccess' => 'function () { $.pjax.reload({container: "#deliveryDetail"}); }',
				],
				'pjaxContainerId' => 'deliveryDetail',
			]),
		],
		[
			'attribute' => 'transport_id',
			'label' => 'Transporte',
			'format' => 'raw',
			'value' => Editable::widget([
				'inputType' => Editable::INPUT_DROPDOWN_LIST,
				'model' => $model,
				'attribute' => 'transport_id',
				'formOptions' => [
					'action' => ['edit-transport', 'id' => $model->id],
					'enableClientValidation' => false,
				],
				'data' => Transport::getIdNameArray(),
				'displayValue' => $model->transport_id ? Transport::getIdNameArray()[$model->transport_id] : '',
				'pluginEvents' => [
					'editableSuccess' => 'function () { $.pjax.reload({container: "#deliveryDetail"}); }',
				],
				'pjaxContainerId' => 'deliveryDetail',
			]),
		],
		[
			'attribute' => 'tracking_number',
			'format' => 'raw',
			'value' => Editable::widget([
				'model' => $model,
				'attribute' => 'tracking_number',
				'formOptions' => [
					'action' => ['edit-tracking-number', 'id' => $model->id],
					'enableClientValidation' => false,
				],
				'pjaxContainerId' => 'deliveryDetail',
			]),
		],
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
			'label' => 'Usuario',
			'attribute' => 'user.username'
		],
		[
			'attribute' => 'create_datetime',
			'format' => 'datetime'
		],
	],
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->getDeliveryStatuses()->with(['user']),
		'pagination' => false,
		'sort' => false,
	]),
]);
?>

<?php Pjax::end() ?>

<?php Pjax::begin(['id' => 'entriesGridviews']) ?>

		<h3>Pedidos</h3>

	<p>
		<?= Html::button('Agregar Pedido', ['class' => 'btn btn-success addEntryButton', 'url' => "$addOrderUrl"]) ?>
	</p>

<?=
GridView::widget([
	'columns' => [
		'id',
		[
			'label' => 'Cliente',
			'value' => 'customer.displayName',
		],
		[
			'label' => 'Estado',
			'value' => 'orderStatus.statusLabel',
		],
		[
			'label' => 'Piezas',
			'value' => function ($model) {
				return $model->totalQuantity;
			},
		],
		[
			'label' => 'Total',
			'value' => function ($model) {
				return $model->total;
			},
			'format' => 'currency',
		],
		'invoiceNumbers',
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{delete}',
			'urlCreator' => function ($action, $order, $key, $index, $actionColumn) use ($model) {
				switch ($action) {
				case 'delete':
					return Url::to(['delete-order', 'deliveryId' => $model->id, 'orderId' => $order->id]);
				}
			},
			'buttons' => [
				'delete' => function ($url, $model, $key) {
					return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'entryDelete']);
				},
			],
		],
	],
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->getOrders()->with(['orderStatus', 'customer', 'orderInvoices', 'orderProducts.product.model'])->orderBy(['id' => SORT_DESC]),
		'pagination' => false,
		'sort' => false,
	]),
]);
?>

		<h3>Reclamos</h3>

	<p>
		<?= Html::button('Agregar Reclamo', ['class' => 'btn btn-success addEntryButton', 'url' => "$addIssueUrl"]) ?>
	</p>

<?=
GridView::widget([
	'columns' => [
		'id',
		[
			'label' => 'Cliente',
			'value' => 'customer.displayName',
		],
		[
			'label' => 'Tipo',
			'value' => 'issueType.name',
		],
		[
			'label' => 'Estado',
			'value' => 'issueStatus.statusLabel',
		],
		'invoiceNumbers',
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{delete}',
			'urlCreator' => function ($action, $issue, $key, $index, $actionColumn) use ($model) {
				switch ($action) {
				case 'delete':
					return Url::to(['delete-issue', 'deliveryId' => $model->id, 'issueId' => $issue->id]);
				}
			},
			'buttons' => [
				'delete' => function ($url, $model, $key) {
					return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'entryDelete']);
				},
			],
		],
	],
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->getIssues()->with(['issueStatus', 'customer', 'issueType', 'issueInvoices'])->orderBy(['id' => SORT_DESC]),
		'pagination' => false,
		'sort' => false,
	]),
]);
?>

		<?php Pjax::end() ?>
</div>
