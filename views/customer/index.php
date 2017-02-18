<?php

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
		<?= Html::a('Importar Clientes', ['import'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Administrar Zonas', ['/zone/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'gecom_id',
            'name',
            [
				'attribute' => 'tax_situation',
				'value' => 'taxSituationLabel',
			],
            'address',
            // 'zip_code',
            // 'locality',
            // 'phone_number',
            // 'doc_number',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
