<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use app\models\Product;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProductsForm */

$this->title = 'Agregar Productos';
?>
<div class="order-product-create">

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
			$form->field($model, 'productIds')->label('Productos')->widget(Select2::classname(), [
				'initValueText' => array_map(function ($id) { return Product::findOne($id)->code; }, $model->productIds ? $model->productIds : []),
				'showToggleAll' => false,
				'options' => [
		      'placeholder' => 'Elegir producto',
		    ],
				'pluginOptions' => [
					'multiple' => true,
					'minimumInputLength' => 3,
					'ajax' => [
						'url' => Url::to('/product/list-available'),
					],
				],
			])
			?>

			<?= $form->field($model, 'quantity') ?>

			<?= $form->field($model, 'ignoreStock')->checkbox(['label' => 'Dejo de valija']) ?>

		    <div class="form-group">
		        <?= Html::submitButton('Añadir', ['class' => 'btn btn-success']) ?>
		    </div>

		    <?php ActiveForm::end(); ?>

		</div>
	</div>

</div>
