<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ZonesImportForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Importar Zonas';
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['/customer/index']];
$this->params['breadcrumbs'][] = ['label' => 'Zonas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-import">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="zones-import-form">
		
		<?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['enctype' => 'multipart/form-data']]) ?>

			<?= $form->field($model, 'file')->fileInput() ?>

			<div class="form-group">
				<?= Html::submitButton('Importar', ['class' => 'btn btn-success']) ?>
			</div>

		<?php ActiveForm::end() ?>

	</div>

</div>
