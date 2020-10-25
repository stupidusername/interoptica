<?php

use app\assets\SuitcaseAsset;
use app\widgets\modal\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Suitcase */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => 'Valijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
	'id' => 'addBrand',
	'url' => Url::to(['add-brand', 'id' => $model->id]),
]);
Modal::end();

SuitcaseAsset::register($this);
?>

<div class="suitcase-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
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

    <h3>Marcas</h3>
    <p>
        <?= Html::button('Agregar Marcas', ['id' => 'addBrandButton', 'class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['id' => 'brandsGridview']) ?>
    <?=
    GridView::widget([
        'columns' => [
            [
                'label' => 'Marca',
                'value' => 'brand.name',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
                    switch ($action) {
                        case 'delete':
                            return Url::to(['remove-brand', 'id' => $model->id]);
                    }
                },
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'removeBrand']);
                    },
                ],
            ],
        ],
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->getSuitcaseBrands()->with(['brand']),
            'pagination' => false,
            'sort' => false,
        ]),
    ]);
    ?>
    <?php Pjax::end() ?>

</div>
