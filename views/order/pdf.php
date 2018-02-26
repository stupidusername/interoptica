<?php

/* @var \app\models\Order $model */

const ENTRIES_PER_PAGE = 24;

// Prepare OrderProduct entries for table distribution
$table = [];

$orderProducts = $model->getOrderProducts()->joinWith(['product.model.brand'])->orderBy([
	'brand.name' => SORT_ASC,
	'model.type' => SORT_ASC,
	'code' => SORT_ASC,
	])->all();

foreach ($orderProducts as $k => $orderProduct) {
	$page = $k / ENTRIES_PER_PAGE;
	$row = $k % (ENTRIES_PER_PAGE / 1);
	$column = $k % ENTRIES_PER_PAGE >= ENTRIES_PER_PAGE / 1 ? 1 : 0;
	$table[$page][$row][$column] = $orderProduct;
}
?>

<?php foreach ($table as $pageNumber => $pages): ?>
	<table cellpadding="0" cellspacing="0" class="t1" style="margin-top: 0px;">
		<tbody>
			<tr>
				<td class="tr5 td11"><p class="p0">Cant.</p></td>
				<td class="tr5 td30"><p class="p0">Marca</p></td>
				<td class="tr5 td29"><p class="p0">Tipo</p></td>
				<td class="tr5 td25"><p class="p0">Modelo</p></td>
				<td class="tr5 td27"><p class="p0">Código</p></td>
				<td class="tr5 td28"><p class="p0">Obser.</p></td>
				<td class="tr5 td23"><p class="p0">Precio</p></td>
				<td class="tr5 td31"><p class="p0">Subtotal</p></td>
			</tr>
			<tr>
				<td class="tr7 td11"></td>
			</tr>
			<?php foreach ($pages as $row): ?>
				<tr>
					<?php foreach ($row as $column => $orderProduct): ?>
						<td class="tr14 td11"><p class="p0"><?= $orderProduct->quantity ?></p></td>
						<td class="tr14 td30"><p class="p0"><?= $orderProduct->product->model->brand->name ?></p></td>
						<td class="tr14 td29"><p class="p0"><?= $orderProduct->product->model->typeLabel ?></p></td>
						<td class="tr14 td25"><p class="p0"><?= $orderProduct->product->model->name ?></p></td>
						<td class="tr14 td27"><p class="p0"><?= $orderProduct->product->code ?></p></td>
						<td class="tr14 td28"><p class="p0"><?= $orderProduct->ignore_stock ? 'Dejó de valija' : '' ?></p></td>
						<td class="tr14 td23"><p class="p0"><?= Yii::$app->formatter->asCurrency($orderProduct->price) ?></p></td>
						<td class="tr14 td31"><p class="p0"><?= Yii::$app->formatter->asCurrency($orderProduct->subtotal) ?></p></td>
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
