<?php

use app\assets\ProductIndexAsset;
use app\models\Brand;
use app\models\Model;
use kartik\editable\Editable;
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

ProductIndexAsset::register($this);
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
		<?= Html::a('Crear Producto', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Administrar Marcas', ['brand/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Administrar Modelos', ['model/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Administrar Colecciones', ['collection/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Administrar Valijas', ['suitcase/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Estadísticas', ['statistics'], ['class' => 'btn btn-primary']) ?>
        <?= Html::button('Habilitar Edición de Stock', ['id' => 'enable-stock-edition', 'class' => 'btn btn-primary']) ?>
        <?= Html::button('Deshabilitar Edición de Stock', ['id' => 'disable-stock-edition', 'class' => 'btn btn-danger', 'style' => 'display: none;']) ?>
    </p>

	<?php
	$columns = [
    [
      'attribute' => 'model.brand.name',
      'label' => 'Marca',
      'filter' => Html::activeDropDownList($searchModel, 'brandId', Brand::getIdNameArray(), ['class' => 'form-control', 'prompt' => 'Elegir marca']),
    ],
    [
      'label' => 'Tipo',
      'attribute' => 'model.typeLabel',
      'filter' => Html::activeDropDownList($searchModel, 'modelType', Model::typeLabels(), ['class' => 'form-control', 'prompt' => 'Elegir tipo']),
    ],
    [
      'attribute' => 'model.name',
      'label' => 'Modelo',
      'filter' => Html::activeTextInput($searchModel, 'modelName', ['class' => 'form-control']),
    ],
		'code',
        'barcode',
		[
			'class' => EditableColumn::className(),
			'attribute' => 'price',
			'format' => 'currency',
			'editableOptions'=> [
				'formOptions' => ['action' => ['edit']],
				'inputType' => '\kartik\money\MaskMoney',
			],
            'refreshGrid' => true,
		],
    [
			'class' => EditableColumn::className(),
			'attribute' => 'stock',
			'editableOptions'=> [
				'formOptions' => ['action' => ['edit']],
                'editableValueOptions' => ['class' => 'stock-editable kv-editable-value kv-editable-link'],
			],
            'refreshGrid' => true,
		],
    [
			'attribute' => 'running_low',
			'value' => function($model) {
				return $model->running_low ? 'Sí' : 'No';
			},
      'filter' => Html::activeDropDownList($searchModel, 'running_low', [0 => 'No', 1 => 'Sí'], ['class' => 'form-control', 'prompt' => 'Elegir estado']),
		],
    [
			'class' => EditableColumn::className(),
			'attribute' => 'available',
      'value' => function($model) {
				return $model->available ? 'Sí' : 'No';
			},
			'editableOptions'=> [
        'inputType' => Editable::INPUT_DROPDOWN_LIST,
				'formOptions' => ['action' => ['edit']],
        'data' => [0 => 'No', 1 => 'Sí'],
			],
      'refreshGrid' => true,
      'filter' => Html::activeDropDownList($searchModel, 'available', [0 => 'No', 1 => 'Sí'], ['class' => 'form-control', 'prompt' => 'Elegir estado']),
		],
    [
			'attribute' => 'running_low_date',
			'format' => 'date',
		],
	];

    $viewColumns = $columns;
    unset($viewColumns[array_search('barcode', $viewColumns, true)]);
	?>

	<?=
	GridView::widget([
    'pjax' => true,
    'pjaxSettings' => [
      'options' => [
        'id' => 'productsGridview'
      ]
    ],
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => array_merge(
        $viewColumns,
      [
        [
    			'label' => 'Editar ' . Html::a('<span id="edit_clear" class="glyphicon glyphicon-remove"></span>'),
    			'encodeLabel' => false,
    			'format' => 'raw',
    			'value' => function ($model, $key, $index, $column) {
    				return Html::a('<span id="edit_uncheck_' . $model->id . '" style="display: none;" class="edit_uncheck glyphicon glyphicon-check"></span>') .
    						Html::a('<span id="edit_check_' . $model->id . '" style="display: block;" class="edit_check glyphicon glyphicon-unchecked"></span>');
    			},
    		],
        [
          'class' => 'yii\grid\ActionColumn',
          'buttons' => [
            'delete' => function ($url) {
              return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                'title' => Yii::t('yii', 'Delete'),
                'aria-label' => Yii::t('yii', 'Delete'),
                'onclick' => "if (confirm('¿Está seguro de eliminar este elemento?')) { $.ajax('$url', { type: 'POST' }).done(function(data) { $.pjax.reload({container: '#productsGridview'}); , timeout: 30000}); } return false;"
              ]);
            },
          ],
        ],
      ]),
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
