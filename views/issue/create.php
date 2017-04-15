<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Issue */

$this->title = 'Crear Reclamo';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
