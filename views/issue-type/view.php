<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\IssueType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => 'Tipos de Reclamo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-view">

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
            'name',
        ],
    ]) ?>

</div>
