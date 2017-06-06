<?php
/* @var app\models\Order $model */

// Split comment into lines
$commentBreaked = wordwrap($model->comment, 65, "\n", true);
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
			<td class="tr0 td4"><p class="p0">CONTROLES EFECTUADOS</p></td>
		</tr>
		<tr>
			<td class="tr6 td0"><p class="p0">Fecha:</p></td>
			<td class="tr6 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr6 td2"><p class="p0"><?= Yii::$app->formatter->asDate($model->enteredOrderStatus->create_datetime, 'dd/MM/yyyy') ?></p></td>
			<td class="tr6 td3"><p class="p0"><?= $commentLines[0] ?></p></td>
			<td class="tr6 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[1] ?></p></td>
			<td class="tr0 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Vendedor:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->user->displayName ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[2] ?></p></td>
			<td class="tr0 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[3] ?></p></td>
			<td class="tr0 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Zona:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->zone ? $model->customer->zone->gecom_id : null ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[4] ?></p></td>
			<td class="tr0 td4"><p class="p0">Factura Nro.:</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">Cliente:</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->displayName ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[5]; ?></p></td>
			<td class="tr0 td4"><p class="p0">Modificar: N</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->address ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[6]; ?></p></td>
			<td class="tr0 td4"><p class="p0">Saldo:</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->locality ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[7]; ?></p></td>
			<td class="tr0 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->province ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[8]; ?></p></td>
			<td class="tr0 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->zip_code ?></p></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[9]; ?></p></td>
			<td class="tr0 td4"><p class="p0">Fecha de Fact.:</p></td>
		</tr>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0"><?= $model->customer->phone_number ?></td>
			<td class="tr0 td3"><p class="p0"><?= $commentLines[10]; ?></p></td>
			<td class="tr0 td4"><p class="p0">&nbsp;</p></td>
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
			<td class="tr12 td17"><p class="p0">Transporte:</p></td>
			<td class="tr12 td2"><p class="p0">Detalle:</p></td>
		</tr>
	</tbody>
</table>
