<?php

use app\models\Product;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\IssueProduct */

$this->title = 'Añadir Reclamo';
?>
<div class="delivery-issue-create">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">

<div class="delivery-issue-form">

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
	$form->field($model, 'issue_id')->label('Reclamo')->widget(Select2::classname(), [
		'initValueText' => $model->issue_id,
		'options' => ['placeholder' => 'Ingresar número de reclamo o cliente'],
		'pluginOptions' => [
			'minimumInputLength' => 1,
			'ajax' => [
				'url' => Url::to('/issue/list'),
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
