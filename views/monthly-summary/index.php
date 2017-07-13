<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;

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
			'label' => 'Mes',
			'value' => function ($model) {
				return Yii::$app->formatter->asDate($model->begin_date, 'MMMM');
			},
			'filter' => array_combine(range(1, 12), array_map(function ($value) {
				return Yii::$app->formatter->asDate("2000-$value-01", 'MMMM');
			}, range(1, 12))),
		],
		[
			'attribute' => 'year',
			'label' => 'Año',
			'value' => function ($model) {
				return Yii::$app->formatter->asDate($model->begin_date, 'yyyy');
			},
		],
		[
			'class' => EditableColumn::className(),
			'attribute' => 'invoiced',
			'format' => 'currency',
			'editableOptions'=> [
				'formOptions' => ['action' => ['edit']],
				'inputType' => '\kartik\money\MaskMoney',
			],
		],
		[
			'class' => EditableColumn::className(),
			'attribute' => 'objective',
			'editableOptions'=> ['formOptions' => ['action' => ['edit']]],
		],
	],
]); ?>
</div>
