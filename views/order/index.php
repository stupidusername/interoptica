<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Url;
use app\models\OrderStatus;

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
    </p>
	
	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'rowOptions' => function ($model, $index, $widget, $grid) {
			return $model->status == OrderStatus::STATUS_ENTERED ? ['style'=>"font-weight: bold;"] : [];
		},
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
					'initValueText' => $searchModel->customer_id ? $searchModel->customer->name : null,
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
				'attribute' => 'comment',
				'contentOptions' => ['style' => 'width: 400px; max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'],
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]);
	?>
</div>
