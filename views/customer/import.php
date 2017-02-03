<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CustomersImportForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Importar Clientes';
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
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
