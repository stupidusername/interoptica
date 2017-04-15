<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reclamos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Crear Reclamo', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Administrar Tipos de Reclamos', ['/issue-type/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
				'attribute' => 'id',
				'contentOptions' => ['style' => 'width: 100px;'],
			],
            'user_id',
            'customer_id',
            [
				'attribute' => 'order_id',
				'contentOptions' => ['style' => 'width: 100px;'],
			],
            'product_id',
            'issue_type_id',
            'comment:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
