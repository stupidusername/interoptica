<?php

use app\assets\SalesmanAsset;
use app\models\SalesmanSuitcase;
use app\widgets\modal\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Vendedores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
	'id' => 'addSuitcase',
	'url' => Url::to(['add-suitcase', 'id' => $model->id]),
	'options' => [
		'tabindex' => false // important for Select2 to work properly
	],
]);
Modal::end();

SalesmanAsset::register($this);
?>

<div class="salesman-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
			'email',
			'gecom_id',
        ],
    ]) ?>

    <h3>Valijas</h3>
    <p>
        <?= Html::button('Agregar Valija', ['id' => 'addSuitcaseButton', 'class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['id' => 'suitcasesGridview']) ?>
    <?=
    GridView::widget([
        'columns' => [
            [
                'label' => 'Cliente',
                'value' => 'customer.displayName',
            ],
			[
                'label' => 'Valija',
                'value' => 'suitcase.name',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
                    switch ($action) {
                        case 'delete':
                            return Url::to(['remove-suitcase', 'id' => $model->id]);
                    }
                },
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'removeSuitcase']);
                    },
                ],
            ],
        ],
        'dataProvider' => new ActiveDataProvider([
            'query' => SalesmanSuitcase::find()
				->where(['user_id' => $model->id])
				->innerJoinWith(['customer', 'suitcase'])
				->orderBy(['customer.gecom_id' => SORT_ASC, 'suitcase.name' => SORT_ASC]),
            'pagination' => false,
            'sort' => false,
        ]),
    ]);
    ?>
    <?php Pjax::end() ?>

</div>
