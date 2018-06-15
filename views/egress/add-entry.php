<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EgressProduct */

$this->title = 'Añadir Producto';
?>

<div class="order-product-update">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
		<div class="order-product-form">

		    <?= $this->render('_entry-form', ['model' => $model]); ?>

		</div>
	</div>

</div>
