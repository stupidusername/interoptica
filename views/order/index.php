<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Crear Pedido', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
				'attribute' => 'id',
				'contentOptions' => ['style'=>'width: 100px;'],
			],
            [
				'label' => 'Usuario',
				'value' => 'user.username',
			],
            [
				'label' => 'Cliente',
				'value' => 'customer.name',
			],
			[
				'label' => 'Estado',
				'value' => 'orderStatus.statusLabel',
			],
			[
				'attribute' => 'comment',
				'format' => 'ntext',
				'options' => ['style' => 'width: 400px;']
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
