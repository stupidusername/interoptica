<?php
/* @var app\models\Order $model */

// Split comment into lines
$comment = trim(preg_replace('/\s\s+/', ' ', $model->comment));
$commentBreaked = wordwrap($comment, 87, "\n", true);
$commentBreakedLines = explode("\n", $commentBreaked);
$commentLines = ['', '', '', '', '', '', '', '', '', '', ''];
foreach ($commentLines as $k => $commentLine) {
	$commentLines[$k] = $k < count($commentBreakedLines) ? $commentBreakedLines[$k] : '';
}
?>

<table cellpadding="0" cellspacing="0" class="t0">
	<tbody>
		<tr>
			<td class="tr0 td0"><p class="p0">Pedido:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->id ?></p></td>
			<td class="tr0 td3"><p class="p0">CAMBIOS/OBSERVACIONES</td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Descuento:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= Yii::$app->formatter->asPercent($model->discount_percentage / 100) ?></p></td>
			<td class="tr0 td3"><p class="p0">&nbsp;</td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Fecha:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<?php
			$date = '';
			if ($model->enteredOrderStatus) {
				$date = Yii::$app->formatter->asDate($model->enteredOrderStatus->create_datetime, 'dd/MM/yyyy');
			}
			?>
			<td class="tr0 td2"><p class="p0"><?= $date ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[0] ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[1] ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Vendedor:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->user->displayName ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[2] ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[3] ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Zona:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->zone ? $model->customer->zone->gecom_id : null ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[4] ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Cliente:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->displayName ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[5]; ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->address ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[6]; ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->locality ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[7]; ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->province ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[8]; ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->zip_code ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[9]; ?></p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->phone_number ?></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[10]; ?></p></td>
		</tr>
	</tbody>
</table>

<table cellpadding="0" cellspacing="0" class="t0" style="margin-top: 15px">
	<tbody>
		<tr>
			<td class="tr0 td0"><p class="p0">Entrega:</p></td>
			<td class="tr0 td33"><p class="p0"><?= $model->delivery_address . ' - ' . $model->delivery_city . ' - ' . $model->delivery_state . ' - ' . $model->delivery_zip_code; ?></p></td>
		</tr>
	</tbody>
</table>

<table cellpadding="0" cellspacing="0" class="t1">
	<tbody>
		<tr>
			<td class="tr12 td27"><p class="p0">&nbsp;</p></td>
			<td class="tr12 td13"><p class="p0">
				<?= $model->customer->taxSituationLabel . ($model->customer->tax_situation_category ? ' (' . $model->customer->tax_situation_category . ')' : '') ?>
			</p></td>
			<td class="tr12 td17"><p class="p0">Transporte: <?= $model->transport ? $model->transport->name : '' ?></p></td>
			<td class="tr12 td2"><p class="p0">Detalle:</p></td>
		</tr>
	</tbody>
</table>

<table cellpadding="0" cellspacing="0" class="t1" style="margin-top: 0px;">
	<tbody>
		<tr>
			<td class="tr5 td11"><p class="p0">Cant.</p></td>
			<td class="tr5 td30"><p class="p0">Marca</p></td>
			<td class="tr5 td29"><p class="p0">Tipo</p></td>
			<td class="tr5 td32"><p class="p0">Código</p></td>
			<td class="tr5 td28"><p class="p0">Lote</p></td>
			<td class="tr5 td28"><p class="p0">Obser.</p></td>
			<td class="tr5 td23"><p class="p0">Precio</p></td>
			<td class="tr5 td31"><p class="p0">Subtotal</p></td>
		</tr>
		<tr>
			<td class="tr7 td11"></td>
		</tr>
	</tbody>
</table>
