<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use app\models\Product;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-product-form">

    <?php $form = ActiveForm::begin([
		'enableClientValidation' => false,
		'fieldConfig' => [
			'errorOptions' => [
				'class' => 'help-block',
				'encode' => false,
			],
		],
	]); ?>
	
	<?=
	$form->field($model, 'product_id')->label('Producto')->widget(Select2::classname(), [
		'initValueText' => $model->product_id ? $model->product->gecom_desc . ' (' . $model->product->stock . ')' : null,
		'options' => ['placeholder' => 'Elegir producto'],
		'pluginOptions' => [
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => Url::to('/product/list'),
			],
		],
	])
	?>

	<?= $form->field($model, 'quantity') ?>	

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Añadir' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
