<?php

use app\models\IssueStatus;
use app\models\IssueType;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reclamos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Crear Reclamo', ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Administrar Tipos de Reclamos', ['/issue-type/index'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Administrar Tipos de Falla', ['/fail/index'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Estadísticas de Fallas', ['statistics'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Resumen de Fallas', ['fail-summary'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
				'attribute' => 'id',
				'contentOptions' => ['style' => 'width: 100px;'],
			],
            [
				'label' => 'Usuario',
				'value' => 'user.username',
				'filter' => Select2::widget([
					'initValueText' => $searchModel->user_id ? $searchModel->user->username : null,
					'model' => $searchModel,
					'attribute' => 'user_id',
					'options' => ['placeholder' => 'Elegir usuario'],
					'pluginOptions' => [
						'allowClear' => true,
						'minimumInputLength' => 1,
						'ajax' => [
							'url' => Url::to('/site/user-list'),
						],
					],
				]),
			],
            [
				'label' => 'Cliente',
				'value' => 'customer.displayName',
				'filter' => Select2::widget([
					'initValueText' => $searchModel->customer_id ? $searchModel->customer->name : null,
					'model' => $searchModel,
					'attribute' => 'customer_id',
					'options' => ['placeholder' => 'Elegir cliente'],
					'pluginOptions' => [
						'allowClear' => true,
						'minimumInputLength' => 3,
						'ajax' => [
							'url' => Url::to('/customer/list'),
						],
					],
				]),
			],
            [
				'attribute' => 'order_id',
				'contentOptions' => ['style' => 'width: 100px;'],
			],
            [
				'label' => 'Asunto',
				'value' => 'issueType.name',
				'filter' => Html::activeDropDownList($searchModel, 'issue_type_id', IssueType::getIdNameArray(), ['class' => 'form-control', 'prompt' => 'Elegir asunto']),
			],
			[
				'label' => 'Estado',
				'value' => 'issueStatus.statusLabel',
				'filter' => Html::activeDropDownList($searchModel, 'status', IssueStatus::statusLabels(), ['class' => 'form-control', 'prompt' => 'Elegir estado']),
			],
			[
				'label' => 'Fecha de Ingreso',
				'value' => 'openIssueStatus.create_datetime',
				'format' => 'datetime'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
