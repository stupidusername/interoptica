<?php

use app\assets\CustomerViewAsset;
use app\models\IssueStatus;
use app\models\IssueType;
use app\models\Order;
use app\models\OrderStatus;
use kartik\select2\Select2;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $orderSearchModel app\models\OrderSearch */
/* @var $orderDataProvider yii\data\ActiveDataProvider */
/* @var $issueSearchModel app\models\IssueSearch */
/* @var $issueDataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

CustomerViewAsset::register($this);
?>
<div class="customer-view">

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


    <?= $this->render('_detail', ['model' => $model]) ?>

	<p>
		<?= Html::a('Crear Envio', ['/delivery/create'], ['class' => 'btn btn-success', 'id' => 'createDelivery']) ?>
	</p>

	<h2>Pedidos</h2>

	<?php Pjax::begin(['id' => 'orders-gridview', 'enablePushState' => false]); ?>
	<?=
	GridView::widget([
		'dataProvider' => $orderDataProvider,
		'filterModel' => $orderSearchModel,
		'rowOptions' => function ($model, $index, $widget, $grid) {
			return $model->status == OrderStatus::STATUS_ENTERED ? ['style'=>"font-weight: bold;"] : [];
		},
		'columns' => [
			[
				'attribute' => 'id',
				'value' => function ($model, $key, $index, $column) {
					return Html::a($model->id, ['order/view', 'id' => $model->id]);
				},
				'format' => 'raw',
				'contentOptions' => ['style' => 'width: 100px;'],
			],
			[
				'label' => 'Usuario',
				'value' => 'user.username',
				'filter' => Select2::widget([
					'initValueText' => $orderSearchModel->user_id ? $orderSearchModel->user->username : null,
					'model' => $orderSearchModel,
					'attribute' => 'user_id',
					'options' => ['placeholder' => 'Elegir usuario'],
					'pluginOptions' => [
						'allowClear' => true,
						'minimumInputLength' => 1,
						'ajax' => [
							'url' => Url::to('/site/user-list'),
						],
					],
				]),
			],
			[
				'class' => 'kartik\grid\EditableColumn',
				'value' => 'orderStatus.statusLabel',
				'filter' => Html::activeDropDownList($orderSearchModel, 'status', OrderStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
				'editableOptions' => function ($model, $key, $index) {
					return[
						'inputType' => 'dropDownList',
						'data' => OrderStatus::statusLabels(),
						'model' => $model,
						'attribute' => 'status',
						'formOptions' => [
							'action' => ['/order/view/', 'id' => $model->id],
						],
					];
				},
			],
			[
				'attribute' => 'comment',
				'contentOptions' => ['style' => 'width: 400px; max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'],
			],
			[
				'label' => 'Enviar ' . Html::a('<span id="order_clear" class="glyphicon glyphicon-remove"></span>'),
				'encodeLabel' => false,
				'format' => 'raw',
				'value' => function ($model, $key, $index, $column) {
          if ($model->status < OrderStatus::STATUS_WAITING_FOR_TRANSPORT) {
  					return Html::a('<span id="order_uncheck_' . $model->id . '" style="display: none;" class="order_uncheck glyphicon glyphicon-check"></span>') .
  							Html::a('<span id="order_check_' . $model->id . '" style="display: block;" class="order_check glyphicon glyphicon-unchecked"></span>');
          }
				},
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'controller' => 'order',
				'template' => '{view}',
			],
		],
	]);
	?>
	<?php Pjax::end(); ?>

	<h2>Reclamos</h2>

	<?php Pjax::begin(['id' => 'issues-gridview', 'enablePushState' => false]); ?>
	<?= GridView::widget([
        'dataProvider' => $issueDataProvider,
        'filterModel' => $issueSearchModel,
        'columns' => [

            [
				'attribute' => 'id',
				'value' => function ($model, $key, $index, $column) {
					return Html::a($model->id, ['issue/view', 'id' => $model->id]);
				},
				'format' => 'raw',
				'contentOptions' => ['style' => 'width: 100px;'],
			],
            [
				'label' => 'Usuario',
				'value' => 'user.username',
				'filter' => Select2::widget([
					'initValueText' => $issueSearchModel->user_id ? $issueSearchModel->user->username : null,
					'model' => $issueSearchModel,
					'attribute' => 'user_id',
					'options' => ['placeholder' => 'Elegir usuario'],
					'pluginOptions' => [
						'allowClear' => true,
						'minimumInputLength' => 1,
						'ajax' => [
							'url' => Url::to('/site/user-list'),
						],
					],
				]),
			],
            [
				'attribute' => 'order_id',
				'contentOptions' => ['style' => 'width: 100px;'],
			],
            [
				'label' => 'Asunto',
				'value' => 'issueType.name',
				'filter' => Html::activeDropDownList($issueSearchModel, 'issue_type_id', IssueType::getIdNameArray(), ['class' => 'form-control', 'prompt' => 'Elegir asunto']),
			],
			[
				'class' => 'kartik\grid\EditableColumn',
				'value' => 'issueStatus.statusLabel',
				'filter' => Html::activeDropDownList($issueSearchModel, 'status', IssueStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
				'editableOptions' => function ($model, $key, $index) {
					return[
						'inputType' => 'dropDownList',
						'data' => IssueStatus::statusLabels(),
						'model' => $model,
						'attribute' => 'status',
						'formOptions' => [
							'action' => ['/issue/view/', 'id' => $model->id],
						],
					];
				},
			],
			[
				'label' => 'Fecha de Ingreso',
				'value' => 'openIssueStatus.create_datetime',
				'format' => 'datetime'
			],
			[
				'label' => 'Enviar ' . Html::a('<span id="issue_clear" class="glyphicon glyphicon-remove"></span>'),
				'encodeLabel' => false,
				'format' => 'raw',
				'value' => function ($model, $key, $index, $column) {
          if ($model->status < IssueStatus::STATUS_WAITING_FOR_TRANSPORT) {
  					return Html::a('<span id="issue_uncheck_' . $model->id . '" style="display: none;" class="issue_uncheck glyphicon glyphicon-check"></span>') .
  							Html::a('<span id="issue_check_' . $model->id . '" style="display: block;" class="issue_check glyphicon glyphicon-unchecked"></span>');
          }
				},
			],

            [
				'class' => 'yii\grid\ActionColumn',
				'controller' => 'issue',
				'template' => '{view}',
			],
        ],
    ]); ?>
	<?php Pjax::end(); ?>

</div>
