<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Product;

/* @var $this yii\web\View */
/* @var $model app\models\IssueComment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-comment-form">

    <?php $form = ActiveForm::begin([
		'enableClientValidation' => false,
		'fieldConfig' => [
			'errorOptions' => [
				'class' => 'help-block',
				'encode' => false,
			],
		],
	]); ?>
	
	<?= $form->field($model, 'comment')->textarea() ?>	

    <div class="form-group">
        <?= Html::submitButton('Añadir', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
