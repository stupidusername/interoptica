<?php

use app\models\IssueStatus;
use app\models\IssueType;
use app\models\OrderStatus;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'gecom_id',
            'name',
			[
				'label' => 'Zona',
				'value' => $model->zone ? $model->zone->name : '',
			],
            [
				'attribute' => 'tax_situation',
				'value' => $model->taxSituationLabel,
			],
            'address',
            'zip_code',
			'province',
            'locality',
            'phone_number',
            'doc_number',
        ],
    ]) ?>
	
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
				'label' => 'Estado',
				'value' => 'orderStatus.statusLabel',
				'filter' => Html::activeDropDownList($orderSearchModel, 'status', OrderStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
			],
			[
				'attribute' => 'comment',
				'contentOptions' => ['style' => 'width: 400px; max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'],
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'controller' => 'order',
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
				'label' => 'Estado',
				'value' => 'issueStatus.statusLabel',
				'filter' => Html::activeDropDownList($issueSearchModel, 'status', IssueStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
			],
			[
				'label' => 'Fecha de Ingreso',
				'value' => 'openIssueStatus.create_datetime',
				'format' => 'datetime'
			],

            [
				'class' => 'yii\grid\ActionColumn',
				'controller' => 'issue',
			],
        ],
    ]); ?>
	<?php Pjax::end(); ?>
	
</div>
