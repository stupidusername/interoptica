<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
?>

<?=
DetailView::widget([
	'model' => $model,
	'attributes' => [
		'id',
		'gecom_id',
		'name',
		'email',
		[
			'attribute' => 'discount_percentage',
			'format' => 'percent',
			'value' => function ($model) {
				return $model->discount_percentage / 100;
			},
		],
		[
			'label' => 'Zona',
			'value' => $model->zone ? $model->zone->name : '',
		],
		[
			'attribute' => 'tax_situation',
			'value' => $model->taxSituationLabel,
		],
		'tax_situation_category',
		[
			'attribute' => 'ivaWithDefault',
			'value' => $model->ivaWithDefault / 100,
			'format' => 'percent',
		],
		'address',
		'zip_code',
		'province',
		'locality',
		'phone_number',
		'cuit',
	],
])
?>
