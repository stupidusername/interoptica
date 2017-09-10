<?php

use app\models\MonthlySummary;
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
if ($orderModel->period == OrderSummary::PERIOD_MONTH) {
	$objectives = MonthlySummary::find()->select(['user_id', 'period' => 'SUBDATE(begin_date, DAYOFMONTH(begin_date) - 1)', 'objective'])
		->andWhere(['>=', 'begin_date', $orderModel->queryFromDate])->andWhere(['<', 'begin_date', $orderModel->queryToDate])
		->andWhere(['user_id' => $userIds])->asArray()->indexBy(function ($row) { return $row['user_id'] . '-' . $row['period']; })->all();
}
$series = [];
$totals = [];
foreach ($userIds as $k => $id) {
	$series[$k * 2] = ['type' => 'column', 'name' => $users[$id]->username, 'data' => [], 'key' => $id];
	if ($orderModel->period == OrderSummary::PERIOD_MONTH) {
		$series[$k * 2 + 1] = ['type' => 'errorbar', 'name' => 'Objetivo', 'data' => []];
	}
	foreach ($periods as $period) {
		if (!isset($totals[$period])) {
			$totals[$period] = 0;
		}
		$idx = $id . '-' . $period;
		$quantity = isset($ordersBySalesman[$idx]) ? (int) $ordersBySalesman[$idx]->totalQuantity : 0;
		$totals[$period] += $quantity;
		$series[$k * 2]['data'][] = $quantity;
		if ($orderModel->period == OrderSummary::PERIOD_MONTH) {
			$objective = isset($objectives[$idx]) ? (int) $objectives[$idx]['objective'] : 0;
			$series[$k * 2 + 1]['data'][] = [$quantity, $objective];
		}
	}
}
$xAxisLabels = array_map(function ($period) use ($totals) { return $period . ' | Total: ' . $totals[$period]; }, $periods);
?>

<?=
Highcharts::widget([
	'scripts' => [
		'highcharts-more',
	],
	'setupOptions' => [
		'lang' => [
			'decimalPoint' => ',',
			'thousandsSep' => '.',
		],
	],
	'options' => [
		'chart' => ['type' => 'column'],
		'title' => ['text' => 'Ventas'],
		'xAxis' => ['categories' => $xAxisLabels],
		'yAxis' => ['title' => ['text' => 'Piezas']],
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
		'series' => array_values($series),
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
