<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use app\models\Product;

/* @var $this yii\web\View */
/* @var $model app\models\CollectionProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="collection-product-form">

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
		'initValueText' => $model->product_id ? $model->product->code . ' (' . $model->product->stock . ')' : null,
		'options' => ['placeholder' => 'Elegir producto'],
		'pluginOptions' => [
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => Url::to('/product/list'),
			],
		],
	])
	?>

    <div class="form-group">
        <?= Html::submitButton('Añadir', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
