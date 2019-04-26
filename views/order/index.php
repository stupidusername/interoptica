<?php

use app\models\OrderStatus;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

    <p>
		<?= Html::a('Crear Pedido', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Administrar Transportes', ['transport/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Administrar Condiciones', ['order-condition/index'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Estadísticas', ['statistics'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php
  	$columns = [
      [
				'attribute' => 'id',
				'value' => function ($model, $key, $index, $column) {
					return Html::a($model->id, ['view', 'id' => $model->id]);
				},
				'format' => 'raw',
				'contentOptions' => ['style' => 'width: 100px;'],
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
				'label' => 'Cliente',
				'value' => 'customer.displayName',
				'filter' => Select2::widget([
					'initValueText' => $searchModel->customer_id ? $searchModel->customer->displayName : null,
					'model' => $searchModel,
					'attribute' => 'customer_id',
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
				'label' => 'Estado',
				'value' => 'orderStatus.statusLabel',
				'filter' => Html::activeDropDownList($searchModel, 'status', OrderStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
			],
      [
        'attribute' => 'dateRange',
        'filterType' => GridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
          'convertFormat' => true,
          'pluginOptions' => [
            'autoApply' => true,
            'locale' => [
              'format' => 'Y-m-d',
            ],
          ],
        ],
  			'label' => 'Fecha de Ingreso',
  			'value' => 'enteredOrderStatus.create_datetime',
  			'format' => 'datetime'
  		],
			[
				'attribute' => 'comment',
				'contentOptions' => ['style' => 'width: 400px; max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'],
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
    ];
    ?>

	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'rowOptions' => function ($model, $index, $widget, $grid) {
			return $model->status == OrderStatus::STATUS_ENTERED ? ['style'=>"font-weight: bold;"] : [];
		},
		'columns' => array_merge($columns, [['class' => 'yii\grid\ActionColumn']]),
	]);
	?>

  <?php
  $columns[0]['value'] = null;
  $columns[7]['format'] = 'decimal';
  ?>

  <?=
	ExportMenu::widget([
		'dataProvider' => $exportDataProvider,
		'target' => ExportMenu::TARGET_SELF,
		'showConfirmAlert' => false,
		'filename' => 'pedidos',
		'columns' => $columns,
	]);
	?>
</div>
