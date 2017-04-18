<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use app\models\IssueStatus;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $model app\models\Issue */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Borrar', ['delete', 'id' => $model->id], [
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
            [
				'label' => 'Usuario',
				'value' => $model->user->username,
			],
            [
				'label' => 'Cliente',
				'value' => $model->customer_id ? $model->customer->name : null,
			],
            'order_id',
            [
				'label' => 'Producto',
				'value' => $model->product_id ? $model->product->gecom_desc : null,
			],
            [
				'label' => 'Tipo',
				'value' => $model->issueType->name,
			],
			[
				'label' => 'Estado',
				'format' => 'raw',
				'value' => Editable::widget([
					'inputType' => Editable::INPUT_DROPDOWN_LIST,
					'model' => $model,
					'attribute' => 'status',
					'data' => IssueStatus::statusLabels(),
					'displayValue' => $model->issueStatus->statusLabel,
					'pluginEvents' => [
						'editableSuccess' => 'function () { $.pjax.reload({container: "#issueStatusGridview"}); }',
					],
				]),
			],
            'comment:ntext',
            'contact',
        ],
    ]) ?>
	
	<h3>Seguimiento de Estados</h3>

	<?php Pjax::begin(['id' => 'issueStatusGridview']) ?>
	<?=
	GridView::widget([
		'columns' => [
			[
				'attribute' => 'status',
				'value' => 'statusLabel',
			],
			[
				'label' => 'Usuario',
				'attribute' => 'user.username'
			],
			[
				'attribute' => 'create_datetime',
				'format' => 'datetime'
			],
		],
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getIssueStatuses()->with(['user']),
			'pagination' => false,
			'sort' => false,
		]),
	]);
	?>
	<?php Pjax::end() ?>

</div>
