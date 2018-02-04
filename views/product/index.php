<?php

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
		<?= Html::a('Administrar Marcas', ['brand/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Administrar Modelos', ['model/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Administrar Colecciones', ['collection/index'], ['class' => 'btn btn-primary']) ?>
    </p>

	<?php
	$columns = [
    [
      'attribute' => 'model.brand.name',
      'label' => 'Marca',
    ],
    [
      'attribute' => 'model.name',
      'label' => 'Modelo',
    ],
		'code',
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
			'attribute' => 'running_low',
			'value' => function($model) {
				return $model->running_low ? 'Sí' : 'No';
			},
      'filter' => Html::activeDropDownList($searchModel, 'running_low', [0 => 'No', 1 => 'Sí'], ['class' => 'form-control', 'prompt' => 'Elegir estado']),
		],
    [
			'attribute' => 'running_low_date',
			'format' => 'date',
		],
	];
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
      $columns,
      [
        [
          'class' => 'yii\grid\ActionColumn',
          'buttons' => [
            'delete' => function ($url) {
              return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                'title' => Yii::t('yii', 'Delete'),
                'aria-label' => Yii::t('yii', 'Delete'),
                'onclick' => "if (confirm('¿Está seguro de eliminar este elemento?')) { $.ajax('$url', { type: 'POST' }).done(function(data) { $.pjax.reload({container: '#productsGridview'}); }); } return false;"
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
