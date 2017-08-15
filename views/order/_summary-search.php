<?php

use app\models\OrderSummary;
use yii\helpers\Html;
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

<?= $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
	'dateFormat' => 'yyyy-MM-dd',
	'options' => ['class' => 'form-control'],
]) ?>

<?= $form->field($model, 'toDate')->widget(DatePicker::classname(), [
	'dateFormat' => 'yyyy-MM-dd',
	'options' => ['class' => 'form-control'],
]) ?>

<?= $form->field($model, 'period')->dropDownList(OrderSummary::periodLabels()); ?>

    <div class="form-group">
	<?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
