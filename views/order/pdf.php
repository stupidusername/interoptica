<?php

use Yii;

/* @var \app\models\Order $model */

const ENTRIES_PER_PAGE = 46;

// Prepare OrderProduct entries for table distribution
$table = [];
foreach ($model->orderProducts as $k => $orderProduct) {
	$page = $k / ENTRIES_PER_PAGE;
	$row = $k % (ENTRIES_PER_PAGE / 2);
	$column = $k % ENTRIES_PER_PAGE >= ENTRIES_PER_PAGE / 2 ? 1 : 0;
	$table[$page][$row][$column] = $orderProduct;
}
?>

<?php foreach ($table as $pageNumber => $pages): ?>
	<table cellpadding="0" cellspacing="0" class="t1" style="margin-top: 0px;">
		<tbody>
			<tr>
				<td class="tr5 td11"><p class="p0">Cant.</p></td>
				<td class="tr5 td27"><p class="p0">Modelo</p></td>
				<td class="tr5 td13"><p class="p0">Obser.</p></td>
				<td class="tr5 td23"><p class="p0">Precio</p></td>
				<td class="tr5 td26"><p class="p0">Subtotal</p></td>
				<td class="tr5"><p class="p0">&nbsp;</p></td>
				<td class="tr5 td11"><p class="p0">Cant.</p></td>
				<td class="tr5 td27"><p class="p0">Modelo</p></td>
				<td class="tr5 td25"><p class="p0">Obser.</p></td>
				<td class="tr5 td25"><p class="p0">Precio</p></td>
				<td class="tr5 td13"><p class="p0">Subtotal</p></td>
			</tr>
			<tr>
				<td class="tr7 td11"></td>
			</tr>
			<?php foreach ($pages as $row): ?>
				<tr>
					<?php foreach ($row as $column => $orderProduct): ?>
						<td class="tr14 td11"><p class="p0"><?= $orderProduct->quantity ?></p></td>
						<td class="tr14 td27"><p class="p0"><?= $orderProduct->product->gecom_desc ?></p></td>
						<td class="tr14 td13"><p class="p0">&nbsp;</p></td>
						<td class="tr14 td25"><p class="p0"><?= Yii::$app->formatter->asCurrency($orderProduct->price) ?></p></td>
						<td class="tr14 td13"><p class="p0"><?= Yii::$app->formatter->asCurrency($orderProduct->subtotal) ?></p></td>
						<?php if ($column == 0): ?>
							<td class="tr14"><p class="p0">&nbsp;</p></td>
						<?php endif; ?>	
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php if ($pageNumber != count($table) - 1): ?>
		<pagebreak />
	<?php endif; ?>
<?php endforeach; ?>

<table cellpadding="0" cellspacing="0" class="t1" >
	<tbody>
		<tr>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Cantidad de Piezas Vendidas:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= $model->totalQuantity ?></p></td>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Subtotal:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= Yii::$app->formatter->asCurrency($model->subtotal) ?></p></td>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Descuento:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= Yii::$app->formatter->asCurrency($model->discountedFromSubtotal) ?></p></td>
			<td class="tr5 td11"><p class="p0">&nbsp;</p></td>
			<td class="tr5"><p class="p0">Total:</p></td>
			<td class="tr5 td23" style="text-align: right"><p class="p0"><?= Yii::$app->formatter->asCurrency($model->total) ?></p></td>
		</tr>
	</tbody>
</table>