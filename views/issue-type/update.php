<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\IssueType */

$this->title = 'Editar Tipo de Reclamo: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => 'Tipos de Reclamo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="issue-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
