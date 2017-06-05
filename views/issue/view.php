<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\IssueAsset;
use app\models\IssueStatus;
use app\widgets\modal\Modal;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $model app\models\Issue */

$this->title = 'Reclamo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$addEntryUrl = Url::to(['add-entry', 'issueId' => $model->id]);

Modal::begin([
	'id' => 'addEntry',
	'url' => $addEntryUrl,
	'options' => [
		'tabindex' => false // important for Select2 to work properly
	],
]);

Modal::end(); 

Modal::begin([
	'id' => 'addComment',
	'url' => Url::to(['add-comment', 'issueId' => $model->id]),
	'options' => [
		'tabindex' => false // important for Select2 to work properly
	],
]);

Modal::end();

IssueAsset::register($this);
?>
<div class="issue-view">

    <h1><?= Html::encode($this->title) ?></h1>
	
	<?php Pjax::begin(['id' => 'issueErrors']) ?>
	<?php if ($model->issueType->required_issue_product && empty($model->issueProducts)): ?>
		<div class="error-summary">
			<h4>Este reclamo requiere la carga de productos.</h4>
		</div>
	<?php endif; ?>
	<?php Pjax::end() ?>

    <p>
		<?= Html::button('Mostrar/Ocultar Detalle del Cliente', ['class' => 'btn btn-primary', 'onclick' => '$("#customerDetail").toggle()']) ?>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Borrar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro de eliminar este elemento?',
                'method' => 'post',
            ],
        ]) ?>
		<?= Html::a('Crear Etiqueta', ['get-envelope', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
				'value' => $model->customer->displayName,
			],
            'order_id',
            [
				'label' => 'Asunto',
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
            'contact',
        ],
    ]) ?>
	
	<div id="customerDetail" style="display: none">
		
		<h3>Detalle del Cliente</h3>
		
		<?= $this->render('/customer/_detail', ['model' => $model->customer]) ?>
		
	</div>
	
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
	
	<h3>Productos</h3>
	
	<p>
		<?= Html::button('Agregar Producto', ['id' => 'addEntryButton', 'class' => 'btn btn-success', 'url' => "$addEntryUrl"]) ?>
	</p>
	
	<?php Pjax::begin(['id' => 'productsGridview']); ?>
	<?=
	GridView::widget([
		'columns' => [
			'product.gecom_desc',
			'quantity',
			[
				'label' => 'Falla',
				'value' => 'fail.name'
			],
			'comment:ntext',
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete}',
				'urlCreator' => function ($action, $model, $key, $index, $actionColumn) {
					switch ($action) {
						case 'update':
							return Url::to(['update-entry', 'issueId' => $model->issue_id, 'productId' => $model->product_id]);
						case 'delete':
							return Url::to(['delete-entry', 'issueId' => $model->issue_id, 'productId' => $model->product_id]);
					}
				},
				'buttons' => [
					'update' => function ($url, $model, $key) {
						return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]), $url, ['class' => 'productUpdate']);
					},
					'delete' => function ($url, $model, $key) {
						return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), $url, ['class' => 'productDelete']);
					},
				],
			],
		],
		'dataProvider' => new ActiveDataProvider([
            'query' => $model->getIssueProducts()->with(['product', 'fail'])->orderBy(['id' => SORT_DESC]),
			'pagination' => false,
			'sort' => false,
        ]),
	]);
	?>
	<?php Pjax::end(); ?>
	
	<h3>Comentarios</h3>
	
	<p>
		<?= Html::button('Agregar Comentario', ['id' => 'addCommentButton', 'class' => 'btn btn-success']) ?>
	</p>
	
	<?php Pjax::begin(['id' => 'commentsGridview']); ?>
	<?=
	GridView::widget([
		'columns' => [
			[
				'label' => 'Usuario',
				'value' => 'user.username',
			],
			[
				'attribute' => 'create_datetime',
				'format' => 'datetime'
			],
			'comment:ntext',
		],
		'dataProvider' => new ActiveDataProvider([
            'query' => $model->getIssueComments()->with(['user']),
			'pagination' => false,
			'sort' => false,
        ]),
	]);
	?>
	<?php Pjax::end(); ?>

</div>
