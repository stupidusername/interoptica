<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Suitcase */

$this->title = 'Crear Valija';
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => 'Valijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="suitcase-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
