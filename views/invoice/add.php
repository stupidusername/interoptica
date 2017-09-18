<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrderInvoice|app\models\IssueInvoice */

$this->title = 'Añadir Factura';
?>
<div class="invoice-create">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
		<?= $this->render('_form', [
			'model' => $model,
		]) ?>
	</div>

</div>
