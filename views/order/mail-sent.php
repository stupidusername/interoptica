<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $success bool */
?>

<?php if ($success): ?>
	<div class="alert alert-success">
		El email ha sido enviado con exito.
	</div>
<?php else: ?>
	<div class="alert alert-danger">
		Ha ocurrido un error durante el envio del email.
	</div>
<?php endif; ?>

<?= Html::a('Volver', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
