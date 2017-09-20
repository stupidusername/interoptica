<?php

use app\models\ProductsImportForm;
use kartik\export\ExportMenu;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;
use yii\helpers\Html;

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
		'gecom_code',
		'gecom_desc',
		[
			'class' => EditableColumn::className(),
			'attribute' => 'price',
			'format' => 'currency',
			'editableOptions'=> [
				'formOptions' => ['action' => ['edit']],
				'inputType' => '\kartik\money\MaskMoney',
			],
		],
		[
			'class' => EditableColumn::className(),
			'attribute' => 'stock',
			'editableOptions'=> [
				'formOptions' => ['action' => ['edit']],
			],
		],
		[
			'attribute' => 'extra',
			'value' => function($model) {
				return $model->extra ? 'Sí' : 'No';
			},
		],
	];
	?>

	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => array_merge($columns, [['class' => 'yii\grid\ActionColumn']]),
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
