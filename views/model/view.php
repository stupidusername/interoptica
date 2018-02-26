<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => 'Modelos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-view">

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
              'label' => 'Tipo',
              'value' => $model->typeLabel,
            ],
            [
              'label' => 'Origen',
              'value' => $model->originLabel,
            ],
            [
              'label' => 'Marca',
              'value' => $model->brand->name,
            ],
            'name',
            'description:ntext',
            'materialNames',
            'front_size',
            'lens_width',
            'bridge_size',
            'temple_length',
            'base',
            [
              'attribute' => 'flex',
              'value' => $model->flex ? 'Sí' : 'No',
            ],
        ],
    ]) ?>

</div>
