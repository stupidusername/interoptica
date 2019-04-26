<?php

use kartik\money\MaskMoney;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderCondition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-condition-form">

    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'interest_rate_percentage')->widget(MaskMoney::classname(), ['pluginOptions' => ['prefix' => '']]) ?>

    <?= $form->field($model, 'editable_in_order')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
