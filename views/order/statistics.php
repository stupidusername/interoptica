<?php

use app\models\MonthlySummary;
use app\models\OrderSummary;
use app\models\User;
use miloschuman\highcharts\Highcharts;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $orderModel app\models\OrderSummary */
/* @var $ordersBySalesman app\models\OrderSummary */
/* @var $this yii\web\View */

$this->title = 'Estadísticas';
$this->params['breadcrums'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrums'][] = ['label' => $this->title];
?>

<h2>Vendedores</h2>

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
$users = User::find()->active()->andWhere(['id' => $userIds])->all();
if ($orderModel->period == OrderSummary::PERIOD_MONTH) {
	$objectives = MonthlySummary::find()->select(['user_id', 'period' => 'SUBDATE(begin_date, DAYOFMONTH(begin_date) - 1)', 'objective'])
		->andWhere(['>=', 'begin_date', $orderModel->queryFromDate])->andWhere(['<', 'begin_date', $orderModel->queryToDate])
		->andWhere(['user_id' => $userIds])->asArray()->indexBy(function ($row) { return $row['user_id'] . '-' . $row['period']; })->all();
}
$series = [];
$subtotalSeries = [];
$totals = [];
$subtotalTotals = [];
foreach ($users as $k => $user) {
	$series[$k * 2] = ['type' => 'column', 'name' => $user->username, 'data' => [], 'key' => $user->id];
	$subtotalSeries[$k * 2] = ['type' => 'column', 'name' => $user->username, 'data' => [], 'key' => $user->id];
	if ($orderModel->period == OrderSummary::PERIOD_MONTH) {
		$series[$k * 2 + 1] = ['type' => 'errorbar', 'name' => 'Objetivo', 'data' => []];
	}
	foreach ($periods as $period) {
		if (!isset($totals[$period])) {
			$totals[$period] = 0;
		}
		$idx = $user->id . '-' . $period;
		$quantity = isset($ordersBySalesman[$idx]) ? (int) $ordersBySalesman[$idx]->totalQuantity : 0;
		$subtotal = isset($ordersBySalesman[$idx]) ? (int) $ordersBySalesman[$idx]->totalSubtotal : 0;
		if (isset($totals[$period])) {
			$totals[$period] += $quantity;
		} else {
			$totals[$period] = 0;
		}
		if (isset($subtotalTotals[$period])) {
			$subtotalTotals[$period] += $subtotal;
		} else {
			$subtotalTotals[$period] = 0;
		}
		$series[$k * 2]['data'][] = $quantity;
		$subtotalSeries[$k * 2]['data'][] = ['y' => $subtotal];
		if ($orderModel->period == OrderSummary::PERIOD_MONTH) {
			$objective = isset($objectives[$idx]) ? (int) $objectives[$idx]['objective'] : 0;
			$series[$k * 2 + 1]['data'][] = [$quantity, $objective];
		}
	}
}
?>

<?php
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

<?php
$xAxisLabels = array_map(function ($period) use ($subtotalTotals) { return $period . ' | Total: ' . Yii::$app->formatter->asCurrency($subtotalTotals[$period]); }, $periods);
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
		'title' => ['text' => 'Subtotales'],
		'xAxis' => ['categories' => $xAxisLabels],
		'yAxis' => ['title' => ['text' => 'Pesos']],
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
		'series' => array_values($subtotalSeries),
	],
]);
?>
