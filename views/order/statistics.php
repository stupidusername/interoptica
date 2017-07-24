<?php

use app\models\User;
use miloschuman\highcharts\Highcharts;

/* @var $model app\models\OrderSummary */
/* @var $ordersBySalesman app\models\OrderSummary */
/* @var $this yii\web\View */

$this->title = 'Estadísticas';
$this->params['breadcrums'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrums'][] = ['label' => $this->title];
?>

<?php echo $this->render('_summary-search', ['model' => $model]); ?>

<?php
$weeks = [];
$week = gmdate('Y-m-d', strtotime('monday ' . $model->fromDate));
while ($week <= gmdate('Y-m-d', strtotime($model->toDate))) {
	$weeks[] = $week;
	$week = gmdate('Y-m-d', strtotime('monday next week ' . $week));
}
$userIds = Yii::$app->authManager->getUserIdsByRole('salesman');
$users = User::find()->andWhere(['id' => $userIds])->indexBy('id')->all();
$series = [];
foreach ($userIds as $k => $id) {
	$series[$k] = ['name' => $users[$id]->username, 'data' => []];
	foreach ($weeks as $week) {
		$series[$k]['data'][] = isset($ordersBySalesman[$id . '-' . $week]) ? (int) $ordersBySalesman[$id . '-' . $week]->totalQuantity : 0;
	}
}
?>

<?=
Highcharts::widget([
	'options' => [
		'chart' => ['type' => 'column'],
		'title' => ['text' => 'Ventas'],
		'xAxis' => ['categories' => $weeks],
		'yAxis' => ['title' => ['text' => 'Piezas vendidas']],
		'plotOptions' => [
			'column' => [
				'dataLabels' => ['enabled' => true],
			],
		],
		'series' => $series,
	],
]);
?>
