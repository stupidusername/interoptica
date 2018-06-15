<?php

use app\models\Egress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Egress */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="egress-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'reason')->dropDownList(Egress::reasonLabels(), ['prompt' => 'Elegir motivo']) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
