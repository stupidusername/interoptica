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
			'label' => 'Zona',
			'value' => $model->zone ? $model->zone->name : '',
		],
		[
			'attribute' => 'tax_situation',
			'value' => $model->taxSituationLabel,
		],
		'tax_situation_category',
		'address',
		'zip_code',
		'province',
		'locality',
		'phone_number',
		'doc_number',
	],
])
?>

