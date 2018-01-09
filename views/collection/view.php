<?php

use app\assets\CollectionAsset;
use app\widgets\modal\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Collection */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => 'Colecciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$addEntryUrl = Url::to(['add-entry', 'collectionId' => $model->id]);

CollectionAsset::register($this);

Modal::begin([
	'id' => 'addEntry',
	'url' => $addEntryUrl,
	'options' => [
		'tabindex' => false // important for Select2 to work properly
	],
]);
Modal::end();
?>
<div class="collection-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Borrar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro de eliminar este elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
        ],
    ]) ?>

    <h3>Productos</h3>

  	<p>
  		<?= Html::button('Agregar Producto', ['id' => 'addEntryButton', 'class' => 'btn btn-success', 'url' => "$addEntryUrl"]) ?>
			<?= Html::a('Editar Productos', ['edit-products', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
  	</p>

    <?php Pjax::begin(['id' => 'productsGridview']); ?>
  	<?=
  	GridView::widget([
      'columns' => [
        'product.code',
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{delete}',
          'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
            switch ($action) {
              case 'delete':
                return Url::to(['delete-entry', 'collectionId' => $model->collection_id, 'productId' => $model->product_id]);
            }
          },
          'buttons' => [
            'update' => function ($url, $model, $key) {
              return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]), $url, ['class' => 'productUpdate']);
            },
            'delete' => function ($url, $model, $key) {
              return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'productDelete']);
            },
          ],
        ],
      ],
      'dataProvider' => new ActiveDataProvider([
        'query' => $model->getCollectionProducts()->with(['product']),
        'pagination' => false,
        'sort' => false,
      ]),
    ]);
  	?>
  	<?php Pjax::end(); ?>

</div>
