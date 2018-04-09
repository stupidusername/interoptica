<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clientes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Crear Cliente', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Administrar Zonas', ['/zone/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php
    $columns = [
        [
          'attribute' => 'id',
          'contentOptions' => ['style'=>'width: 100px;'],
        ],
        'gecom_id',
        'name',
        'email',
        'address',
        'locality',
    ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'columns' => array_merge($columns, [['class' => 'yii\grid\ActionColumn']]),
    ]); ?>

    <?=
  	ExportMenu::widget([
  		'dataProvider' => $exportDataProvider,
  		'target' => ExportMenu::TARGET_SELF,
  		'showConfirmAlert' => false,
  		'filename' => 'clientes',
  		'columns' => $columns,
  	]);
  	?>
</div>
