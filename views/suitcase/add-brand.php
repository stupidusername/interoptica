<?php

use app\models\Brand;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInvoice|app\models\IssueInvoice */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Añadir Factura';
?>

<div class="invoice-create">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
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

			<?= $form->field($model, 'brand_id')->label('Marca')->dropDownList(Brand::getIdNameArray(), ['prompt' => 'Elegir marca']) ?>

		    <div class="form-group">
		        <?= Html::submitButton('Añadir', ['class' => 'btn btn-success']) ?>
		    </div>

		    <?php ActiveForm::end(); ?>

		</div>
	</div>

</div>
