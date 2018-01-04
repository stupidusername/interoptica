<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CollectionProduct */

$this->title = 'Añadir Producto';
?>
<div class="collection-product-create">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
		<?= $this->render('_entry-form', [
			'model' => $model,
		]) ?>
	</div>

</div>
