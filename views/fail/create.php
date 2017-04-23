<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Fail */

$this->title = 'Crear Falla';
$this->params['breadcrumbs'][] = ['label' => 'Reclamos', 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => 'Fallas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
