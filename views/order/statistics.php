<?php

use app\models\OrderSummary;
use app\models\User;
use miloschuman\highcharts\Highcharts;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $orderModel app\models\OrderSummary */
/* @var $ordersBySalesman app\models\OrderSummary */
/* @var $billingModel app\models\BillingSummary */
/* @var $billingBySalesman app\models\BillingSummary */
/* @var $this yii\web\View */

$this->title = 'Estadísticas';
$this->params['breadcrums'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrums'][] = ['label' => $this->title];
?>

<h2>Piezas Vendidas</h2>

<?php echo $this->render('_summary-search', ['model' => $orderModel]); ?>

<?php
$periods = [];
$period = $orderModel->queryFromDate;
while ($period < $orderModel->queryToDate) {
	$periods[] = $period;
	switch ($orderModel->period) {
	case OrderSummary::PERIOD_WEEK:
		$period = gmdate('Y-m-d', strtotime('monday next week ' . $period));
		break;
	case OrderSummary::PERIOD_MONTH:
		$period = gmdate('Y-m-d', strtotime('+1 month ' . $period));
		break;
	case OrderSummary::PERIOD_YEAR:
		$period = gmdate('Y-m-d', strtotime('+1 year ' . $period));
		break;
	}
}
$userIds = Yii::$app->authManager->getUserIdsByRole('salesman');
$users = User::find()->andWhere(['id' => $userIds])->indexBy('id')->all();
$series = [];
foreach ($userIds as $k => $id) {
	$series[$k] = ['name' => $users[$id]->username, 'data' => [], 'key' => $id];
	foreach ($periods as $period) {
		$series[$k]['data'][] = isset($ordersBySalesman[$id . '-' . $period]) ? (int) $ordersBySalesman[$id . '-' . $period]->totalQuantity : 0;
	}
}
?>

<?=
Highcharts::widget([
	'setupOptions' => [
		'lang' => [
			'decimalPoint' => ',',
			'thousandsSep' => '.',
		],
	],
	'options' => [
		'chart' => ['type' => 'column'],
		'title' => ['text' => 'Ventas'],
		'xAxis' => ['categories' => $periods],
		'yAxis' => ['title' => ['text' => 'Piezas vendidas']],
		'plotOptions' => [
			'column' => [
				'dataLabels' => ['enabled' => true],
			],
			'series' => [
				'cursor' => 'pointer',
				'point' => [
					'events' => [
						'click' => new JsExpression('function () { location.href = "' . Url::to(['index', 'OrderSearch' => ['user_id' => '']]) . '" + this.series.userOptions.key; }'),
					],
				],
			],
		],
		'series' => $series,
	],
]);
?>

<h2>Facturación Mensual</h2>

<?php echo $this->render('_billing-summary-search', ['model' => $billingModel]); ?>

<?php
$periods = [];
$period = $billingModel->queryFromDate;
while ($period < $billingModel->queryToDate) {
	$periods[] = $period;
	$period = gmdate('Y-m-d', strtotime('+1 month ' . $period));
}
$series = [];
foreach ($userIds as $k => $id) {
	$series[$k] = ['name' => $users[$id]->username, 'data' => [], 'key' => $id];
	foreach ($periods as $period) {
		$series[$k]['data'][] = isset($billingBySalesman[$id . '-' . $period]) ? (int) $billingBySalesman[$id . '-' . $period]->invoiced : 0;
	}
}
?>

<?=
Highcharts::widget([
	'setupOptions' => [
		'lang' => [
			'decimalPoint' => ',',
			'thousandsSep' => '.',
		],
	],
	'options' => [
		'chart' => ['type' => 'column'],
		'title' => ['text' => 'Total facturado'],
		'xAxis' => ['categories' => $periods],
		'yAxis' => ['title' => ['text' => '$']],
		'plotOptions' => [
			'column' => [
				'dataLabels' => ['enabled' => true, 'format' => '$ {point.y:,.2f}'],
			],
		],
		'tooltip' => [
			'pointFormat' => '<span style="color:{point.color}">●</span> {series.name}: <b>$ {point.y:,.2f}</b><br/>',
		],
		'series' => $series,
	],
]);
?>
