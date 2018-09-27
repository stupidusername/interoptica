<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use zxbodya\yii2\galleryManager\GalleryManager;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

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
        'attribute' => 'model.name',
        'label' => 'Modelo',
      ],
      'code',
      [
        'attribute' => 'polarized',
        'value' => $model->polarized ? 'Sí' : 'No',
      ],
      [
        'attribute' => 'mirrored',
        'value' => $model->mirrored ? 'Sí' : 'No',
      ],
      [
        'attribute' => 'price',
        'format' => 'currency',
      ],
      'colorNames',
      'lensColorNames',
      'stock',
      [
        'attribute' => 'running_low',
        'value' => $model->running_low ? 'Sí' : 'No',
      ],
      [
        'attribute' => 'running_low_date',
        'format' => 'date',
      ],
      [
        'attribute' => 'create_date',
        'format' => 'date',
      ],
      [
        'attribute' => 'update_date',
        'format' => 'date',
      ],
      [
        'attribute' => 'available',
        'value' => $model->available ? 'Sí' : 'No',
      ],
    ],
  ]) ?>

  <h3>Imágenes</h3>

  <?= GalleryManager::widget([
    'model' => $model,
    'behaviorName' => 'galleryBehavior',
    'apiRoute' => 'gallery',
  ]); ?>

</div>
