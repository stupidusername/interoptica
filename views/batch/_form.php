<?php

use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Batch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="batch-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=
  	$form->field($model, 'product_id')->label('Producto')->widget(Select2::classname(), [
  		'initValueText' => $model->product_id ? $model->product->code . ' (' . $model->product->stock . ')' : null,
  		'options' => ['placeholder' => 'Elegir producto'],
  		'pluginOptions' => [
  			'minimumInputLength' => 3,
  			'ajax' => [
  				'url' => Url::to('/product/list'),
  			],
  		],
      'disabled' => !$model->isNewRecord,
  	])
  	?>

    <?= $form->field($model, 'entered_date')->widget(DatePicker::className(), [
      'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd',
      ],
    ]) ?>

    <?= $form->field($model, 'dispatch_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipment_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'initial_stamp_numer')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?php if (!$model->isNewRecord): ?>
      <?= $form->field($model, 'stock')->textInput() ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Editar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
