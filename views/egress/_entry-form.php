<?php

use app\models\Product;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model app\models\EgressProduct */
?>

<?php $form = ActiveForm::begin([
'enableClientValidation' => false,
'fieldConfig' => [
  'errorOptions' => [
    'class' => 'help-block',
    'encode' => false,
  ],
],
]); ?>

<?=
$form->field($model, 'product_id')->label('Producto')->widget(Select2::classname(), [
  'initValueText' => $model->product_id ? $model->product->code . ' (' . $model->product->stock . ')' : '',
  'options' => [
    'placeholder' => 'Elegir producto',
  ],
  'pluginOptions' => [
    'minimumInputLength' => 3,
    'ajax' => [
      'url' => Url::to('/product/list'),
    ],
  ],
])
?>

<?= $form->field($model, 'quantity') ?>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Añadir' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
