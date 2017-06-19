<?php

use app\models\Product;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProduct */

$this->title = 'Añadir Pedido';
?>
<div class="delivery-order-create">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">

<div class="delivery-order-form">

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
	$form->field($model, 'order_id')->label('Pedido')->widget(Select2::classname(), [
		'initValueText' => $model->order_id,
		'options' => ['placeholder' => 'Ingresar número de pedido o cliente'],
		'pluginOptions' => [
			'minimumInputLength' => 1,
			'ajax' => [
				'url' => Url::to('/order/list'),
			],
		],
	])
?>

    <div class="form-group">
	<?= Html::submitButton('Añadir', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>

</div>
