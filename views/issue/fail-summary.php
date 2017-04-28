<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IssueProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resumen de Fallas';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fail-summary">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_issue-product-search', ['model' => $searchModel]); ?>

	<?php
	$columns = [
		'id',
		[
			'label' => 'Producto',
			'value' => 'product.gecom_desc'
		],
		[
			'label' => 'Falla',
			'value' => 'fail.name'
		],
		'quantity',
		'comment:ntext',
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