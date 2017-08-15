<?php

use app\models\OrderSummary;
use app\models\User;
use miloschuman\highcharts\Highcharts;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $model app\models\OrderSummary */
/* @var $ordersBySalesman app\models\OrderSummary */
/* @var $this yii\web\View */

$this->title = 'Estadísticas';
$this->params['breadcrums'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrums'][] = ['label' => $this->title];
?>

<?php echo $this->render('_summary-search', ['model' => $model]); ?>

<?php
$periods = [];
$period = $model->queryFromDate;
while ($period < $model->queryToDate) {
	$periods[] = $period;
	switch ($model->period) {
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
