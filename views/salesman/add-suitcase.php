<?php

use app\models\Customer;
use app\models\Suitcase;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInvoice|app\models\SalesmanSuitcase */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Añadir Valija';
?>

<div class="suitcase-create">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
		<div class="suitcase-form">

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
			$form->field($model, 'customer_id')->label('Cliente')->widget(Select2::classname(), [
				'initValueText' => $model->customer_id ? Customer::findOne($model->customer_id)->displayName : null,
				'options' => ['placeholder' => 'Elegir cliente'],
				'pluginOptions' => [
					'minimumInputLength' => 3,
					'ajax' => [
						'url' => Url::to('/customer/list'),
					],
				],
			])
			?>

			<?= $form->field($model, 'suitcase_id')->label('Valija')->dropDownList(Suitcase::getIdNameArray(), ['prompt' => 'Elegir valija']) ?>

		    <div class="form-group">
		        <?= Html::submitButton('Añadir', ['class' => 'btn btn-success']) ?>
		    </div>

		    <?php ActiveForm::end(); ?>

		</div>
	</div>

</div>
