<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MonthlySummarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vendedores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monthly-summary-index">

    <h1><?= Html::encode($this->title) ?></h1>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
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
			'attribute' => 'month',
			'value' => function ($model) {
				return Yii::$app->formatter->asDate($model->begin_date, 'MMMM');
			},
			'filter' => array_combine(range(1, 12), array_map(function ($value) {
				return Yii::$app->formatter->asDate("2000-$value-01", 'MMMM');
			}, range(1, 12))),
		],
		[
			'attribute' => 'year',
			'value' => function ($model) {
				return Yii::$app->formatter->asDate($model->begin_date, 'yyyy');
			},
		],
		[
			'attribute' => 'invoiced',
			'format' => 'currency',
		],
		'objective',
	],
]); ?>
</div>
