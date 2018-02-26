<?php

use dosamigos\selectize\SelectizeTextInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=
  	$form->field($model, 'model_id')->label('Modelo')->widget(Select2::classname(), [
  		'initValueText' => $model->model_id ? $model->model->name : null,
  		'options' => ['placeholder' => 'Elegir modelo'],
  		'pluginOptions' => [
  			'minimumInputLength' => 3,
  			'ajax' => [
  				'url' => Url::to('/model/list'),
  			],
  		],
  	])
  	?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'polarized')->checkbox() ?>

    <?= $form->field($model, 'mirrored')->checkbox() ?>

    <?= $form->field($model, 'colorNames')->widget(SelectizeTextInput::className(), [
      'loadUrl' => ['list-colors'],
      'options' => ['class' => 'form-control'],
      'clientOptions' => [
          'plugins' => ['remove_button'],
          'valueField' => 'name',
          'labelField' => 'name',
          'searchField' => ['name'],
          'create' => true,
      ],
    ])->hint('Use comas para separar los colores') ?>

    <?= $form->field($model, 'lensColorNames')->widget(SelectizeTextInput::className(), [
      'loadUrl' => ['list-colors'],
      'options' => ['class' => 'form-control'],
      'clientOptions' => [
          'plugins' => ['remove_button'],
          'valueField' => 'name',
          'labelField' => 'name',
          'searchField' => ['name'],
          'create' => true,
      ],
    ])->hint('Use comas para separar los colores') ?>

    <?= $form->field($model, 'price')->widget(MaskMoney::classname()) ?>

    <?= $form->field($model, 'stock')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
