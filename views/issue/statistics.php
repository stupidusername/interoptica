<?php

use app\models\FailSummary;
use miloschuman\highcharts\Highcharts;

/* @var $model app\models\FailSummary */
/* @var $failsByType app\models\FailSummary */
/* @var $failsByProduct app\models\FailSummary */
/* @var $this yii\web\View */

$this->title = 'Estadísticas de Fallas';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_fail-summary-search', ['model' => $model]); ?>


<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">

<?php
$data = array_map(function ($elem) { return ['name' => $elem->fail->name, 'y' => (int) $elem->total_quantity]; }, $failsByType);
?>

<?=
Highcharts::widget([
	'options' => [
		'chart' => ['type' => 'pie'],
		'title' => ['text' => 'Fallas'],
		'plotOptions' => [
			'pie' => [
				'dataLabels' => [
					'enabled' => false,
				],
				'showInLegend' => true,
			],
		],
		'series' => [
				['name' => 'Fallas', 'data' => $data],
		],
	],
]);
?>
		</div>

		<div class="col-md-6">

<?php
$data = array_map(function ($elem) { return ['name' => $elem->product->gecom_desc, 'y' => (int) $elem->total_quantity]; }, $failsByProduct);
?>

<?=
Highcharts::widget([
	'options' => [
		'chart' => ['type' => 'pie'],
		'title' => ['text' => 'Fallas por Producto (Primeros 10)'],
		'plotOptions' => [
			'pie' => [
				'dataLabels' => [
					'enabled' => false,
				],
				'showInLegend' => true,
			],
		],
		'series' => [
				['name' => 'Fallas', 'data' => $data],
		],
	],
]);
?>
		</div>
	</div>
</div>
