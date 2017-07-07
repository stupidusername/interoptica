<?php

use app\models\FailSummary;
use app\models\Product;
use miloschuman\highcharts\Highcharts;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

$this->title = 'Estadísticas de Fallas';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$productsOrderedByFailRate = Product::getProductsOrderedByFailRate(5);
$categories = ArrayHelper::getColumn($productsOrderedByFailRate, 'gecom_desc');
$percentages = ArrayHelper::getColumn($productsOrderedByFailRate, function ($product) {
	return ($product['fails'] / $product['orders']) * 100;
});
?>

<?=
Highcharts::widget([
	'options' => [
		'chart' => ['type' => 'bar'],
		'title' => ['text' => 'Fallas / Ventas (Primeros 5)'],
		'xAxis' => [
			'categories' => $categories,
		],
		'legend' => ['reversed' => 'true'],
		'plotOptions' => [
			'series' => ['stacking' => 'normal'],
		],
		'series' => [
				['name' => 'Porcentaje', 'data' => $percentages],
		],
	],
]);
?>

<?php
$model = new FailSummary;
$fails = $model->search([], ['fail_id'])->query->all();
$data = array_map(function ($elem) { return ['name' => $elem->fail->name, 'y' => (int) $elem->total_quantity]; }, $fails);
?>

<?=
Highcharts::widget([
	'options' => [
		'chart' => ['type' => 'pie'],
		'title' => ['text' => 'Fallas'],
		'plotOptions' => [
			'pie' => [
				'dataLabels' => ['enabled' => false],
				'showInLegend' => true,
			],
		],
		'series' => [
				['name' => 'Fallas', 'data' => $data],
		],
	],
]);
?>

<?php
$model = new FailSummary;
$fails = $model->search([], ['product_id'])->query->orderBy(['total_quantity' => SORT_DESC])->limit(10)->all();
$data = array_map(function ($elem) { return ['name' => $elem->product->gecom_desc, 'y' => (int) $elem->total_quantity]; }, $fails);
?>

<?=
Highcharts::widget([
	'options' => [
		'chart' => ['type' => 'pie'],
		'title' => ['text' => 'Fallas por Producto (Primeros 10)'],
		'plotOptions' => [
			'pie' => [
				'dataLabels' => ['enabled' => false],
				'showInLegend' => true,
			],
		],
		'series' => [
				['name' => 'Fallas', 'data' => $data],
		],
	],
]);
?>
