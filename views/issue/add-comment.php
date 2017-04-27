<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\IssueComment */

$this->title = 'Añadir Comentario';
?>
<div class="issue-comment-create">

	<div id="modal-header">
		<h3><?= Html::encode($this->title) ?></h3>
	</div>

	<div id="modal-body">
		<?= $this->render('_comment-form', [
			'model' => $model,
		]) ?>
	</div>

</div>
