<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Customer;
use app\models\OrderStatus;
use kartik\money\MaskMoney;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_id')->dropDownList(Customer::getIdNameArray(), ['prompt' => 'Elegir cliente']) ?>

    <?= $form->field($model, 'discount_percentage')->widget(MaskMoney::classname(), ['pluginOptions' => ['prefix' => '']]) ?>
	
	<?php if (!$model->isNewRecord): ?>
		<?= $form->field($model, 'status')->dropDownList(OrderStatus::statusLabels(), ['prompt' => 'Elegir estado']); ?>
	<?php endif; ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
