<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FailSummary */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resumen de Fallas';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fail-summary">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_fail-summary-search', ['model' => $searchModel]); ?>

	<?php
	$columns = [
		[
			'label' => 'Producto',
			'value' => 'product.gecom_desc'
		],
		[
			'label' => 'Falla',
			'value' => 'fail.name'
		],
		'total_quantity',
	];
	?>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,
    ]); ?>
	
	<?=
	ExportMenu::widget([
		'dataProvider' => $exportDataProvider,
		'target' => ExportMenu::TARGET_SELF,
		'showConfirmAlert' => false,
		'filename' => 'fallas',
		'columns' => $columns,
	]);
	?>
</div>
