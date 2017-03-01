<?php
/* @var app\models\Order $model */
?>

<table cellpadding="0" cellspacing="0" class="t0">
	<tbody>
		<tr>
			<td class="tr0 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td2"><p class="p0">&nbsp;</p></td>
			<td class="tr0 td3"><p class="p0">CONDICIONES COMERCIALES</p></td>
			<td class="tr0 td4"><p class="p0">CONTROLES EFECTUADOS</p></td>
		</tr>
		<tr>
			<td rowspan="2" class="tr2 td0"><p class="p0">Fecha:</p></td>
			<td class="tr3 td1"><p class="p0">&nbsp;</p></td>
			<td rowspan="2" class="tr2 td2"><p class="p0"><?= Yii::$app->formatter->asDate($model->enteredOrderStatus->create_datetime, 'dd/MM/yyyy') ?></p></td>
			<td class="tr3 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr4 td5"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr5 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr5 td6"><p class="p0">&nbsp;</p></td>
			<td class="tr5 td7"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td colspan="2" rowspan="2" class="tr6 td8"><p class="p0">Vendedor:</p></td>
			<td rowspan="2" class="tr6 td2"><p class="p0"><?= $model->user->gecom_id . ' - ' . $model->user->profile->name ?> </p></td>
			<td class="tr8 td6"><p class="p0">&nbsp;</p></td>
			<td class="tr8 td9"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr9 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr9 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr10 td0"><p class="p0">Zona:</p></td>
			<td class="tr10 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td2"><p class="p0"><?= $model->customer->zone ? $model->customer->zone->gecom_id : null ?></p></td>
			<td class="tr10 td3"><p class="p0">CAMBIOS/OBSERVACIONES</p></td>
			<td class="tr10 td4"><p class="p0">Factura Nro.:</p></td>
		</tr>
		<tr>
			<td class="tr6 td0"><p class="p0">Cliente:</p></td>
			<td colspan="2" class="tr6 td10"><p class="p0"><?= $model->customer->gecom_id . ' ' . $model->customer->name ?></p></td>
			<td class="tr6 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr6 td4"><p class="p0">Modificar: N</p></td>
		</tr>
		<tr>
			<td class="tr10 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td2"><p class="p0"><?= $model->customer->address ?></p></td>
			<td class="tr10 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td4"><p class="p0">Saldo:</p></td>
		</tr>
		<tr>
			<td class="tr10 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td2"><p class="p0"><?= $model->customer->locality ?></p></td>
			<td class="tr10 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr11 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr11 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr11 td2"><p class="p0"><?= $model->customer->province ?></p></td>
			<td class="tr11 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr11 td4"><p class="p0">&nbsp;</p></td>
		</tr>
		<tr>
			<td class="tr10 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td2"><p class="p0"><?= $model->customer->zip_code ?></p></td>
			<td class="tr10 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td4"><p class="p0">Fecha de Fact.:</p></td>
		</tr>
		<tr>
			<td class="tr10 td0"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td1"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td2"><p class="p0"><?= $model->customer->phone_number ?></td>
			<td class="tr10 td3"><p class="p0">&nbsp;</p></td>
			<td class="tr10 td4"><p class="p0">&nbsp;</p></td>
		</tr>
	</tbody>
</table>

<table cellpadding="0" cellspacing="0" class="t1">
	<tbody>
		<tr>
			<td class="tr12 td27"><p class="p0">&nbsp;</p></td>
			<td class="tr12 td13"><p class="p0"><?= $model->customer->tax_situation ?></p></td>
			<td class="tr12 td17"><p class="p0">Transporte:</p></td>
			<td class="tr12 td2"><p class="p0">Detalle:</p></td>
		</tr>
	</tbody>
</table>