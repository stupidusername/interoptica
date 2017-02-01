<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomersImportForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Import Customers';
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-import">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="customer-import-form">
		
		<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

			<?= $form->field($model, 'file')->fileInput() ?>



			<div class="form-group">
				<?= Html::submitButton('Import', ['class' => 'btn btn-success']) ?>
			</div>

		<?php ActiveForm::end() ?>

	</div>

</div>
