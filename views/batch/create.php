<?php

use kartik\date\DatePicker;
use kartik\select2\Select2;
use unclead\multipleinput\TabularInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;


/* @var $this yii\web\View */
/* @var $models [app\models\Batch] */

$this->title = 'Crear Lotes';
$this->params['breadcrumbs'][] = ['label' => 'Lotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
      'id'                        => 'tabular-form',
      'enableAjaxValidation'      => true,
      'enableClientValidation'    => false,
      'validateOnChange'          => false,
      'validateOnSubmit'          => true,
      'validateOnBlur'            => false,
    ]); ?>

    <?= TabularInput::widget([
      'models' => $models,
      'addButtonPosition' => TabularInput::POS_FOOTER,
      'attributeOptions' => [
          'enableAjaxValidation'      => true,
          'enableClientValidation'    => false,
          'validateOnChange'          => false,
          'validateOnSubmit'          => true,
          'validateOnBlur'            => false,
      ],
      'columns' => [
          [
            'name'  => 'product_id',
            'title' => 'Producto',
            'type'  => Select2::className(),
            'options' => function ($model) {
              return [
                'initValueText' => $model->product_id ? $model->product->code . ' (' . $model->product->stock . ')' : null,
                'options' => ['placeholder' => 'Elegir producto'],
                'pluginOptions' => [
                  'minimumInputLength' => 3,
                  'ajax' => [
                    'url' => Url::to('/product/list'),
                  ],
                ],
              ];
            },
          ],
          [
            'name'  => 'entered_date',
            'title' => 'Fecha de ingreso',
            'type'  => DatePicker::className(),
            'value' => function ($model) {
              return $model->entered_date ? $model->entered_date : date('Y-m-d');
            },
            'options' => [
              'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
              ],
            ],
          ],
          [
            'name' => 'dispatch_number',
            'title' => 'Numero de depacho',
          ],
          [
            'name' => 'initial_stamp_number',
            'title' => 'Número de estampilla inicial',
          ],
          [
            'name' => 'quantity',
            'title' => 'Cantidad',
          ],
      ],
    ]) ?>

    <?= Html::submitButton('Crear', ['class' => 'btn btn-success']);?>

    <?php $form->end(); ?>

</div>
