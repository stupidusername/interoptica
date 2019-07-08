<?php
/* @var $this \yii\web\View view component instance */
/* @var $products app\models\Product[] */

use app\models\Product;
?>

<?php if (empty($products)): ?>
    No se registró un estado stock bajo para ningún producto durante la última semana.
<?php else: ?>
    <p>
        Para los siguientes productos se registró un estado de stock bajo durante la última semana:
    </p>

    <ul>
    <?php foreach ($products as $product): ?>
        <li>
            <?= $product->model->brand->name ?> - <?= $product->model->name ?> - <?= $product->code ?>: <?= $product->stock ?> en stock.
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
