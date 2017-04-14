<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\IssueType */

$this->title = 'Crear Tipo de Reclamo';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => 'Tipos de Reclamo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
