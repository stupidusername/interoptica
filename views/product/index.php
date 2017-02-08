<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\ProductsImportForm;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $exportDataProvider yii\data\ActiveDataProvider */

$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
		<?= Html::a('Crear Producto', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Importar Precios', ['import', 'scenario' => ProductsImportForm::SCENARIO_PRICE], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Importar Stock', ['import', 'scenario' => ProductsImportForm::SCENARIO_STOCK], ['class' => 'btn btn-primary']) ?>
    </p>

	<?php
	$columns = [
		['class' => 'yii\grid\SerialColumn'],
		'id',
		'variant_id',
		'gecom_code',
		'gecom_desc',
		'price',
		'stock',
		['class' => 'yii\grid\ActionColumn'],
	];
	?>

	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $columns,
	]);
	?>

	<?=
	ExportMenu::widget([
		'dataProvider' => $exportDataProvider,
		'target' => ExportMenu::TARGET_SELF,
		'showConfirmAlert' => false,
		'filename' => 'productos',
		'columns' => $columns,
	]);
	?>
</div>
