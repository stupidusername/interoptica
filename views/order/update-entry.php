<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use app\models\Product;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProduct */

$this->title = 'Editar Producto: ' . $model->product->code;
?>
<div class="order-product-update">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
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
				'initValueText' => $model->product->code . ' (' . $model->product->stock . ')',
				'options' => [
		      'placeholder' => 'Elegir producto',
		    ],
				'pluginOptions' => [
					'minimumInputLength' => 3,
					'ajax' => [
						'url' => Url::to('/product/list-available'),
					],
				],
			])
			?>

			<?= $form->field($model, 'quantity') ?>

			<?= $form->field($model, 'ignore_stock')->checkbox(['label' => 'Dejo de valija']) ?>

		    <div class="form-group">
		        <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
		    </div>

		    <?php ActiveForm::end(); ?>

		</div>
	</div>

</div>
