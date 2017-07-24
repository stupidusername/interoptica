<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Customer;
use app\models\OrderStatus;
use app\models\Transport;
use app\models\User;
use kartik\money\MaskMoney;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\OrderForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

	<?=
	$form->field($model, 'customer_id')->label('Cliente')->widget(Select2::classname(), [
		'initValueText' => $model->customer_id ? Customer::findOne($model->customer_id)->name : null,
		'options' => ['placeholder' => 'Elegir cliente'],
		'pluginOptions' => [
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => Url::to('/customer/list'),
			],
		],
		'pluginEvents' => [
			'select2:select' => 'function(e) { $("#orderform-customeremail").val(e.params.data.email); }',
		],
	])
	?>

    <?= $form->field($model, 'customerEmail') ?>

	<?php if (!$model->isNewRecord && Yii::$app->user->identity->isAdmin): ?>
		<?= $form->field($model, 'user_id')->label('Usuario')->dropDownList(User::getIdNameArray(), ['prompt' => 'Elegir usuario']) ?>
	<?php endif; ?>

    <?= $form->field($model, 'discount_percentage')->widget(MaskMoney::classname(), ['pluginOptions' => ['prefix' => '']]) ?>
	
	<?php if (!$model->isNewRecord): ?>
		<?= $form->field($model, 'status')->dropDownList(OrderStatus::statusLabels(), ['prompt' => 'Elegir estado']); ?>
	<?php endif; ?>

	<?= $form->field($model, 'transport_id')->label('Transporte')->dropDownList(Transport::getIdNameArray(), ['prompt' => 'Elegir transporte']); ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
