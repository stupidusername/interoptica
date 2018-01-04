<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProduct */

$this->title = 'Editar Producto: ' . $model->product->code;
?>
<div class="order-product-update">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
		<?= $this->render('_entry-form', [
			'model' => $model,
		]) ?>
	</div>

</div>
