<?php

use app\models\Brand;
use app\models\Model;
use dosamigos\selectize\SelectizeTextInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Model */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(Model::typeLabels(), ['prompt' => 'Elegir tipo']) ?>

    <?= $form->field($model, 'brand_id')->dropDownList(Brand::getIdNameArray(), ['prompt' => 'Elegir marca']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'materialNames')->widget(SelectizeTextInput::className(), [
      'loadUrl' => ['list-materials'],
      'options' => ['class' => 'form-control'],
      'clientOptions' => [
          'plugins' => ['remove_button'],
          'valueField' => 'name',
          'labelField' => 'name',
          'searchField' => ['name'],
          'create' => true,
      ],
    ])->hint('Use comas para separar los materiales') ?>

    <?= $form->field($model, 'front_size')->textInput() ?>

    <?= $form->field($model, 'lens_width')->textInput() ?>

    <?= $form->field($model, 'bridge_size')->textInput() ?>

    <?= $form->field($model, 'temple_length')->textInput() ?>

    <?= $form->field($model, 'base')->textInput() ?>

    <?= $form->field($model, 'flex')->checkbox() ?>

    <?= $form->field($model, 'polarized')->checkbox() ?>

    <?= $form->field($model, 'mirrored')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Editar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
