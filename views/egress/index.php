<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Egresos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="egress-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crear Egreso', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
              'attribute' => 'user.username',
              'label' => 'Usuario',
            ],
            [
              'attribute' => 'create_datetime',
              'format' => 'datetime',
            ],
            [
              'attribute' => 'reasonLabel',
              'label' => 'Motivo',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
