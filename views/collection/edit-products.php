<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Collection */
/* @var $searchModel app\models\ProductSearch */

$this->title = 'Editar Productos';
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => 'Colecciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$products = $model->getProducts()->asArray()->all();
$productIds = ArrayHelper::getColumn($products, 'id');
?>
<div class="collection-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => [
          'options' => [
            'id' => 'products-gridview',
          ]
        ],
        'columns' => [
            'code',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{check}',
              'buttons' => [
          			'check' => function ($url, $product, $key) use ($model, $productIds) {
          				$options = array_merge([
          					'title' => 'Chequear',
          					'aria-label' => 'Chequear',
                    'onclick' => 'event.preventDefault(); var url = $(this).attr("href"); jQuery.post(url, function() { $.pjax.reload({container: "#products-gridview"}); });'
          				]);
          				$icon = in_array($product->id, $productIds) ? 'glyphicon-check' : 'glyphicon-unchecked';
          				return Html::a('<span class="glyphicon ' . $icon . '"></span>', $url, $options);
          			},
              ],
              'urlCreator' => function($action, $product, $key, $index) use ($model, $productIds) {
          			$url = [''];
          			switch ($action) {
          				case 'check':
          					$url = [!in_array($product->id, $productIds) ? 'check-entry' : 'delete-entry', 'collectionId' => $model->id, 'productId' => $product->id];
          					break;
          			}
          			return Url::to($url);
                },
            ],
        ],
    ]); ?>
</div>
