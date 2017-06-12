<?php

use app\models\DeliveryStatus;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

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
			'value' => 'deliveryStatus.statusLabel',
			'filter' => Html::activeDropDownList($searchModel, 'status', DeliveryStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
		],
		[
			'label' => 'Fecha de Ingreso',
			'value' => 'enteredDeliveryStatus.create_datetime',
			'format' => 'datetime'
		],
		'transport',

		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{view} {delete}',
		],
	],
]); ?>
</div>
