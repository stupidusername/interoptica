<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Brand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'logoUpload')->widget(FileInput::className(), [
      'options' => [
        'accept' => ['.jpg', '.png'],
      ],
      'pluginOptions' => [
        'initialPreview' => $model->logoUrl,
        'initialPreviewAsData' => true,
        'showUpload' => false,
        'initialPreviewShowDelete' => false,
      ],
      'pluginEvents' => [
        'fileclear' => 'function() { $("#brand-deletelogo").val(1); }',
        'fileloaded' => 'function() { $("#brand-deletelogo").val(""); }',
      ],
    ]); ?>

    <?= $form->field($model, 'deleteLogo')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Editar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
