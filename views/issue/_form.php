<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\IssueType;

/* @var $this yii\web\View */
/* @var $model app\models\Issue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=
	$form->field($model, 'customer_id')->label('Cliente')->widget(Select2::classname(), [
		'initValueText' => $model->customer_id ? $model->customer->displayName : null,
		'options' => ['placeholder' => 'Elegir cliente'],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => Url::to('/customer/list'),
			],
		],
	])
	?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'issue_type_id')->label('Asunto')->dropDownList(IssueType::getIdNameArray(), ['prompt' => 'Elegir asunto']) ?>

    <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
