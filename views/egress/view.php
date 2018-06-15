<?php

use app\assets\EgressAsset;
use app\widgets\modal\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Egress */

$this->title = 'Egreso: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Egresos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$addEntryUrl = Url::to(['add-entry', 'egressId' => $model->id]);

Modal::begin([
	'id' => 'addEntry',
	'url' => $addEntryUrl,
	'options' => [
		'tabindex' => false // important for Select2 to work properly
	],
]);
Modal::end();

EgressAsset::register($this);
?>
<div class="egress-view">

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
            [
              'attribute' => 'user.username',
              'label' => 'Usuario'
            ],
            [
              'attribute' => 'create_datetime',
              'format' => 'datetime',
            ],
            [
              'attribute' => 'reasonLabel',
              'label' => 'Motivo',
            ],
            'comment:ntext',
        ],
    ]) ?>

    <h3>Productos</h3>

  	<p>
  		<?= Html::button('Agregar Producto', ['id' => 'addEntryButton', 'class' => 'btn btn-success', 'url' => "$addEntryUrl"]) ?>
  	</p>

  	<?php Pjax::begin(['id' => 'productsGridview']); ?>
  	<?=
  	GridView::widget([
  		'columns' => [
  			'product.code',
  			'quantity',
  			[
  				'class' => 'yii\grid\ActionColumn',
  				'template' => '{update} {delete}',
  				'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
  					switch ($action) {
  						case 'update':
  							return Url::to(['update-entry', 'egressId' => $model->egress_id, 'productId' => $model->product_id]);
  						case 'delete':
  							return Url::to(['delete-entry', 'egressId' => $model->egress_id, 'productId' => $model->product_id]);
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
        'query' => $model->getEgressProducts()->with(['product'])->orderBy(['id' => SORT_DESC]),
  			'pagination' => false,
  			'sort' => false,
      ]),
  	]);
  	?>
    <? Pjax::end(); ?>

</div>
