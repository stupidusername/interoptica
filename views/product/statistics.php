<?php

use app\models\Brand;
use app\models\Model;
use app\models\OrderStatus;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductStatistics */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Estadíticas de Ventas';
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="product-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <div class="product-statistics-search">

    <?php $form = ActiveForm::begin([
      'method' => 'get',
    ]); ?>

    <?= $form->field($searchModel, 'orderStatus')->dropDownList(OrderStatus::statusLabels(), ['prompt' => 'Elegir estado']); ?>

    <?= $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(), [
    	'dateFormat' => 'yyyy-MM-dd',
    	'options' => ['class' => 'form-control'],
    ]) ?>

    <?= $form->field($searchModel, 'toDate')->widget(DatePicker::classname(), [
    	'dateFormat' => 'yyyy-MM-dd',
    	'options' => ['class' => 'form-control'],
    ]) ?>

    <div class="form-group">
      <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

  </div>

  <?php
  $columns = [
    'id',
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
    'totalQuantity',
    'averagePrice:currency',
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
    'dataProvider' => $dataProvider,
    'target' => ExportMenu::TARGET_SELF,
    'showConfirmAlert' => false,
    'filename' => 'estadisticas-productos',
    'columns' => $columns,
  ]);
  ?>

</div>
