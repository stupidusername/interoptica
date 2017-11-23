<?php
/* @var $this \yii\web\View view component instance */
/* @var $model app\models\Order */
/* @var $delivery app\models\Delivery */
?>

<p>Estimado cliente:</p>

<p>
Le informamos que el número de tracking para su pedido con factura(s) <?= $model->invoiceNumbers ?> es el <?= $delivery->tracking_number ?>.
El mismo fue enviado por el transporte <?= $delivery->transport->name ?>.
</p>

<?= $this->render('_payment-methods') ?>
