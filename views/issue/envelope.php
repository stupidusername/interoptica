<?php
/* @var $model app\models\Issue */
?>

<ul>
	<li>Reclamo: <?= $model->id . ($model->order_id ? ' | Pedido: ' . $model->order_id : '')?></li>
	<li>Cliente: <?= $model->customer->displayName ?></li>
	<li>Dirección: <?= $model->customer->address ?></li>
	<li>Localidad: <?= $model->customer->locality ?></li>
	<li>Provincia: <?= $model->customer->province ?></li>
	<li>Código Postal: <?= $model->customer->zip_code ?></li>
	<li>Teléfono: <?= $model->customer->phone_number ?></li>
	<li>Contacto: <?= $model->contact ?></li>
</ul>