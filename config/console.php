<?php

$local = require(__DIR__ . '/local.php');
$params = require(__DIR__ . '/params.php');
$aliases = require(__DIR__ . '/aliases.php');
$mailer = require(__DIR__ . '/mailer.php');

$config = [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__),
	'bootstrap' => [
		'log',
	],
	'controllerNamespace' => 'app\commands',
	'components' => [
		'cache' => [
			'class' => YII_ENV == 'dev' ? 'yii\caching\DummyCache' : 'yii\caching\MemCache',
		],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'db' => $local['db'],
		'mailer' => $mailer,
	],
	'params' => $params,
	'modules' => [
		'user' => [
            'class' => 'dektrium\user\Module',
        ],
		'rbac' => 'dektrium\rbac\RbacConsoleModule',
	],
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
	];
}

return $config;
