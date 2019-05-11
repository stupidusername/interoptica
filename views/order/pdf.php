<?php

use app\models\Model;

/* @var \app\models\Order $model */

$orderProducts = $model->getOrderProducts()->joinWith(['product.model.brand', 'orderProductBatches.batch'])->orderBy([
	'brand.name' => SORT_ASC,
	'model.type' => SORT_ASC,
	'code' => SORT_ASC,
	])->all();
?>

<table cellpadding="0" cellspacing="0" class="t1" style="margin-top: 0px;">
	<tbody>
			<?php foreach ($orderProducts as $orderProduct): ?>
				<?php $imported = $orderProduct->product->model->origin == Model::ORIGIN_IMPORTED; ?>
				<?php foreach (($imported ? $orderProduct->orderProductBatches : [null]) as $orderProductBatch): ?>
					<?php $decoration = $orderProduct->ignore_stock ? 'style="text-decoration: underline"' : '' ?>
					<tr>
							<td class="tr14 td11"><p class="p0" <?= $decoration ?>><?= $imported ? $orderProductBatch->quantity : $orderProduct->quantity ?></p></td>
							<td class="tr14 td30"><p class="p0" <?= $decoration ?>><?= $orderProduct->product->model->brand->name ?></p></td>
							<td class="tr14 td29"><p class="p0" <?= $decoration ?>><?= $orderProduct->product->model->typeLabel ?></p></td>
							<td class="tr14 td32"><p class="p0" <?= $decoration ?>><?= $orderProduct->product->code ?></p></td>
							<td class="tr14 td28"><p class="p0"><?= $imported ? $orderProductBatch->batch->dispatch_number : '' ?></p></td>
							<td class="tr14 td28"><p class="p0"><?= $orderProduct->ignore_stock ? 'Dejó de valija' : '' ?></p></td>
							<td class="tr14 td23"><p class="p0"><?= Yii::$app->formatter->asCurrency($orderProduct->price) ?></p></td>
							<td class="tr14 td31"><p class="p0"><?= Yii::$app->formatter->asCurrency($orderProduct->subtotal) ?></p></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
	</tbody>
</table>

<table cellpadding="0" cellspacing="0" class="t1" >
	<tbody>
		<tr>
			<td class="tr5"><p class="p0">Piezas&nbsp;Vendidas:</p></td>
			<td class="tr5 td11" style="text-align: right"><p class="p0"><?= $model->totalQuantity ?></p></td>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Subtotal:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= Yii::$app->formatter->asCurrency($model->subtotal) ?></p></td>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Subtotal&nbsp;+&nbsp;IVA:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= Yii::$app->formatter->asCurrency($model->subtotalPlusIva) ?></p></td>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Financiación:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= Yii::$app->formatter->asCurrency($model->financing) ?></p></td>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Total:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= Yii::$app->formatter->asCurrency($model->total) ?></p></td>
		</tr>
	</tbody>
</table>
