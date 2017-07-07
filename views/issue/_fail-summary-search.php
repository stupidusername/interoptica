<?php

use app\models\Fail;
use app\models\Product;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FailSummary */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fail-summary-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <?=
	$form->field($model, 'product_id')->label('Producto')->widget(Select2::classname(), [
		'initValueText' => $model->product_id ? $model->product->gecom_desc . ' (' . $model->product->stock . ')' : null,
		'options' => ['placeholder' => 'Elegir producto'],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 3,
			'ajax' => [
				'url' => Url::to('/product/list'),
			],
		],
	])
	?>

    <?= $form->field($model, 'fail_id')->label('Falla')->dropDownList(Fail::getIdNameArray(), ['prompt' => 'Elegir falla']) ?>
	
	<?= $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
		'dateFormat' => 'yyyy-MM-dd',
		'options' => ['class' => 'form-control'],
	]) ?>
	
	<?= $form->field($model, 'toDate')->widget(DatePicker::classname(), [
		'dateFormat' => 'yyyy-MM-dd',
		'options' => ['class' => 'form-control'],
	]) ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
