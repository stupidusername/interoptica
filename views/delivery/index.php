<?php

use app\models\DeliveryStatus;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeliverySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Envios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
	<?= Html::a('Crear Envio', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
		[
			'attribute' => 'id',
			'value' => function ($model, $key, $index, $column) {
				return Html::a($model->id, ['view', 'id' => $model->id]);
			},
			'format' => 'raw',
			'contentOptions' => ['style' => 'width: 100px;'],
		],
		[
			'label' => 'Clientes',
			'value' => 'customerNames',
			'filter' => Select2::widget([
				'initValueText' => $searchModel->customerId ? $searchModel->customer->displayName : null,
				'model' => $searchModel,
				'attribute' => 'customerId',
				'options' => ['placeholder' => 'Elegir cliente'],
				'pluginOptions' => [
					'allowClear' => true,
					'minimumInputLength' => 3,
					'ajax' => [
						'url' => Url::to('/customer/list'),
					],
				],
			]),
		],
		[
			'label' => 'Usuario',
			'value' => 'user.username',
			'filter' => Select2::widget([
				'initValueText' => $searchModel->user_id ? $searchModel->user->username : null,
				'model' => $searchModel,
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
			'value' => function ($model, $key, $index, $column) {
				return DeliveryStatus::statusLabels()[$model->status];
			},
			'filter' => Html::activeDropDownList($searchModel, 'status', DeliveryStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
		],
		'transport',
		[
			'class' => EditableColumn::className(),
			'attribute' => 'tracking_number',
			'editableOptions'=> [
				'formOptions' => ['action' => ['edit']],
			],
		],
		[
			'label' => 'Fecha de Ingreso',
			'value' => 'enteredDeliveryStatus.create_datetime',
			'format' => 'datetime'
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{view} {delete}',
		],
	],
]); ?>
</div>
