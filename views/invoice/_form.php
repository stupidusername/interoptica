<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInvoice|app\models\IssueInvoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin([
		'enableClientValidation' => false,
		'fieldConfig' => [
			'errorOptions' => [
				'class' => 'help-block',
				'encode' => false,
			],
		],
	]); ?>
	
	<?= $form->field($model, 'number') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Añadir' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
