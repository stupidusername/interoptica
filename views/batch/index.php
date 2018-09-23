<?php

use kartik\grid\GridView;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crear Lotes', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
      				'label' => 'Producto',
      				'value' => 'product.code',
      				'filter' => Select2::widget([
      					'initValueText' => $searchModel->product_id ? $searchModel->product->code : null,
      					'model' => $searchModel,
      					'attribute' => 'product_id',
      					'options' => ['placeholder' => 'Elegir producto'],
      					'pluginOptions' => [
      						'allowClear' => true,
      						'minimumInputLength' => 3,
      						'ajax' => [
      							'url' => Url::to('/product/list'),
      						],
      					],
      				]),
      			],
            [
              'attribute' => 'entered_date',
              'filterType' => GridView::FILTER_DATE,
              'filterWidgetOptions' => [
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                  'autoclose' => true,
                  'format' => 'yyyy-mm-dd',
                ],
              ],
        			'format' => 'date'
        		],
            'dispatch_number',
            'shipment_number',
            'initial_stamp_numer',
            'quantity',
            'stock',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view} {update}'
            ],
        ],
    ]); ?>
</div>
