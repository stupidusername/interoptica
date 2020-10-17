<?php

use app\models\Customer;
use app\models\OrderCondition;
use app\models\OrderStatus;
use app\models\Transport;
use app\models\User;
use kartik\money\MaskMoney;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderForm */
/* @var $form yii\widgets\ActiveForm */

$conditions = OrderCondition::find()->active()->asArray()->all();
$conditionOptions = ArrayHelper::map($conditions, 'id', 'title');
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

	<?=
	$form->field($model, 'customer_id')->label('Cliente')->widget(Select2::classname(), [
		'initValueText' => $model->customer_id ? Customer::findOne($model->customer_id)->displayName : null,
		'options' => ['placeholder' => 'Elegir cliente'],
		'pluginOptions' => [
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => Url::to('/customer/list'),
			],
		],
		'pluginEvents' => [
			'select2:select' => 'function(e) { $("#orderform-customeremail").val(e.params.data.email); $("#orderform-discount_percentage").val(e.params.data.discount_percentage); $("#orderform-discount_percentage-disp").val(parseFloat(e.params.data.discount_percentage).toFixed(2)).trigger("mask.maskMoney"); }',
		],
	])
	?>

    <?= $form->field($model, 'customerEmail') ?>

	<?php if (!$model->isNewRecord && Yii::$app->user->identity->isAdmin): ?>
		<?= $form->field($model, 'user_id')->label('Usuario')->dropDownList(User::getIdNameArray(), ['prompt' => 'Elegir usuario']) ?>
	<?php endif; ?>

    <?= $form->field($model, 'discount_percentage')->widget(MaskMoney::classname(), ['pluginOptions' => ['prefix' => '']]) ?>

    <?= $form->field($model, 'order_condition_id')->dropDownList($conditionOptions, ['prompt' => 'Elegir condición'])->label('Condición de Venta'); ?>

    <?= $form->field($model, 'interest_rate_percentage')->textInput(['readonly' => 'readonly']) ?>

    <div id="interest_rate_percentage_buttons_div" class="form-group">
        <?= Html::button('0%', ['value' => 0, 'class' => 'interest_rate_percentage_button']) ?>
        <?= Html::button('5%', ['value' => 5, 'class' => 'interest_rate_percentage_button']) ?>
        <?= Html::button('10%', ['value' => 10, 'class' => 'interest_rate_percentage_button']) ?>
        <?= Html::button('15%', ['value' => 15, 'class' => 'interest_rate_percentage_button']) ?>
    </div>

    <script type="text/javascript" style="display: none;">
        var conditions = <?= json_encode($conditions); ?>;
        var conditionDropdown = document.getElementById('orderform-order_condition_id');
        var interestInput = document.getElementById('orderform-interest_rate_percentage');
        var interestButtonsDiv = document.getElementById('interest_rate_percentage_buttons_div');
        var interestButtons = document.getElementsByClassName('interest_rate_percentage_button');

        var interestButtonOnClick = function () {
            interestInput.value = this.value;
        };

        for (var i = 0; i < interestButtons.length; i++) {
            interestButtons[i].addEventListener('click', interestButtonOnClick, false);
        }

        var showHideButtons = function() {
            show = false;
            if (conditionDropdown.value !== null) {
                for (var i = 0; i < conditions.length; i++) {
                    if (conditions[i].id == conditionDropdown.value) {
                        if (conditions[i].editable_in_order !== null && conditions[i].editable_in_order != 0) {
                            show = true;
                        }
                        break;
                    }
                }
            }
            interestButtonsDiv.style.display = show ? 'block' : 'none';
        }

        conditionDropdown.onchange = function () {
            showHideButtons();
            value = null;
            for (var i = 0; i < conditions.length; i++) {
                if (conditions[i].id == this.value) {
                    value = conditions[i].interest_rate_percentage;
                    break;
                }
            }
            interestInput.value = value;
        }

        showHideButtons();
    </script>

	<?php if (!$model->isNewRecord): ?>
		<?= $form->field($model, 'status')->dropDownList(OrderStatus::statusLabels(), ['prompt' => 'Elegir estado']); ?>
	<?php endif; ?>

	<?= $form->field($model, 'transport_id')->label('Transporte')->dropDownList(Transport::getIdNameArray(), ['prompt' => 'Elegir transporte']); ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <h3>Dirección de Entrega (Opcional)</h3>

    <?= $form->field($model, 'delivery_address')->label('Calle, Altura, Departamento') ?>

    <?= $form->field($model, 'delivery_city') ?>

    <?= $form->field($model, 'delivery_state') ?>

    <?= $form->field($model, 'delivery_zip_code') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
