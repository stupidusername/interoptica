<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipos de Reclamo';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crear Tipo de Reclamo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'id',
            'name',
			[
				'attribute' => 'required_issue_product',
				'format' => 'boolean',
			],
			[
				'attribute' => 'required_issue_product_fail_id',
				'format' => 'boolean',
			],
			[
				'attribute' => 'required_issue_product_quantity',
				'format' => 'boolean',
			],

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view} {delete}',
            ],
        ],
    ]); ?>
</div>
