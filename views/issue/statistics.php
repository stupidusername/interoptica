<?php

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
		'title' => ['text' => 'Fallas / Ventas (%)'],
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

