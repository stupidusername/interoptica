<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Customer;
use kartik\money\MaskMoney;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'price')->widget(MaskMoney::classname()) ?>

	<?= $form->field($model, 'quantity') ?>	

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Añadir' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
