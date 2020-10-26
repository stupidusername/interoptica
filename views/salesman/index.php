<?php

use app\models\User;
use kartik\export\ExportMenu;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Vendedores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salesman-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'columns' => [
            'username',
            'gecom_id',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
        'dataProvider' => new ActiveDataProvider([
            'query' => User::find()->active()->where(['id' => Yii::$app->authManager->getUserIdsByRole('salesman')])->orderBy(['username' => SORT_ASC]),
            'pagination' => false,
            'sort' => false,
        ]),
    ]); ?>
</div>
