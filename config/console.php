<?php

$local = require(__DIR__ . '/local.php');
$params = require(__DIR__ . '/params.php');

$config = [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__),
	'bootstrap' => [
		'log',
		'queue',
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
		'redis' => require(__DIR__ . '/redis.php'),
		'queue' => require(__DIR__ . '/queue.php'),
		'afip' => require(__DIR__ . '/afip.php'),
	],
	'params' => $params,
	'modules' => [
		'rbac' => 'dektrium\rbac\RbacConsoleModule',
	],
    /*
    'controllerMap' => [
	'fixture' => [ // Fixture generation command line.
	    'class' => 'yii\faker\FixtureController',
	],
    ],
     */
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
	];
}

return $config;
