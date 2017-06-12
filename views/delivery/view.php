<?php

use app\assets\DeliveryAsset;
use app\models\DeliveryStatus;
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
$this->params['breadcrumbs'][] = ['label' => 'Entregas', 'url' => ['index']];
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
				'formOptions' => ['action' => 'edit-status'],
				'data' => DeliveryStatus::statusLabels(),
				'displayValue' => $model->deliveryStatus->statusLabel,
				'pluginEvents' => [
					'editableSuccess' => 'function () { $.pjax.reload({container: "#entriesGridview"}); }',
				],
			]),
		],
		[
			'attribute' => 'transport',
			'format' => 'raw',
			'value' => Editable::widget([
				'model' => $model,
				'attribute' => 'transport',
				'formOptions' => ['action' => 'edit-transport'],
			]),
		],
	],
]) ?>
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
			'label' => 'Estado',
			'value' => 'issueStatus.statusLabel',
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{delete}',
		],
	],
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->getOrders()->with(['orderStatus', 'customer'])->orderBy(['id' => SORT_DESC]),
		'pagination' => false,
		'sort' => false,
	]),
]);
?>

		<h3>Reclamos</h3>

	<p>
		<?= Html::button('Agregar Producto', ['class' => 'btn btn-success addEntryButton', 'url' => "$addIssueUrl"]) ?>
	</p>

<?=
GridView::widget([
	'columns' => [
		'id',
		[
			'label' => 'Tipo',
			'value' => 'issueType.name',
		],
		[
			'label' => 'Estado',
			'value' => 'orderStatus.statusLabel',
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{delete}',
			'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
				switch ($action) {
				case 'delete':
					return Url::to(['delete-issue', 'deliveryId' => $model->delivery_id, 'issueId' => $model->product_id]);
				}
			},
			'buttons' => [
				'delete' => function ($url, $model, $key) {
					return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'productDelete']);
				},
			],
		],
	],
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->getIssues()->with(['issueStatus', 'customer'])->orderBy(['id' => SORT_DESC]),
		'pagination' => false,
		'sort' => false,
	]),
]);
?>

		<?php Pjax::end() ?>
</div>
