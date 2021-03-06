<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
	$items = [
		['label' => 'Home', 'url' => ['/site/index']],
		['label' => 'Clientes', 'url' => ['/customer/index']],
		['label' => 'Productos', 'url' => ['/product/index']],
    ['label' => 'Lotes', 'url' => ['/batch/index']],
    ['label' => 'Egresos', 'url' => ['/egress/index']],
		['label' => 'Pedidos', 'url' => ['/order/index']],
		['label' => 'Reclamos', 'url' => ['/issue/index']],
		['label' => 'Envios', 'url' => ['/delivery/index']],
		['label' => 'Perfil', 'url' => ['/user/settings/profile']],
		Yii::$app->user->isGuest ? (
			['label' => 'Login', 'url' => ['/user/security/login']]
		) : (
			'<li>'
			. Html::beginForm(['/user/security/logout'], 'post')
			. Html::submitButton(
				'Logout (' . Yii::$app->user->identity->username . ')',
				['class' => 'btn btn-link logout']
			)
			. Html::endForm()
			. '</li>'
		)
	];
	if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) {
		$adminItems = [
			['label' => 'Vendedores', 'url' => ['/salesman/index']],
			['label' => 'Usuarios', 'url' => ['/user/admin/index']],
		];
		array_splice($items, count($items) - 2, 0, $adminItems);
	}
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $items,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
