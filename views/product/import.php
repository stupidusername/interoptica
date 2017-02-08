<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ProductsImportForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductsImportForm */
/* @var $form yii\widgets\ActiveForm */

$title = 'Importar ';
switch ($model->scenario) {
	case ProductsImportForm::SCENARIO_PRICE:
		$title .= 'Precios';
		break;
	case ProductsImportForm::SCENARIO_STOCK:
		$title .= 'Stock';
		break;
}

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-import">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="customer-import-form">
		
		<?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['enctype' => 'multipart/form-data']]) ?>

			<?= $form->field($model, 'file')->fileInput() ?>

			<div class="form-group">
				<?= Html::submitButton('Importar', ['class' => 'btn btn-success']) ?>
			</div>

		<?php ActiveForm::end() ?>

	</div>

</div>
