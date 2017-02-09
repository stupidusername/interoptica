<?php

$params = require(__DIR__ . '/params.php');

$config = [
	'id' => 'basic',
	'name' => 'Interoptica',
	'version' => '1.0.0',
	'language' => 'es-AR',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'components' => [
		'request' => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => 'vhAlxUomdeaZU3IhHoMiWUOsNETOBg3F',
		],
		'cache' => [
			'class' => YII_ENV == 'dev' ? 'yii\caching\DummyCache' : 'yii\caching\MemChache',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => YII_ENV == 'dev',
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
					[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'db' => require(__DIR__ . '/db.php'),
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
		'assetManager' => [
			'linkAssets' => true,
		],
		'view' => [
			'theme' => [
				'pathMap' => [
					'@dektrium/user/views' => '@app/views/user'
				],
			],
		],
		'formatter' => [
			'class' => 'yii\i18n\Formatter',
			'nullDisplay' => '',
		],
	],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'enableRegistration' => false,
			'enableAccountDelete' => false,
			'emailChangeStrategy' => \dektrium\user\Module::STRATEGY_SECURE,
			'confirmWithin' => 1209600, // confirm within 2 weeks
			'recoverWithin' => 1209600,
			'admins' => [
				'admin',
			],
			'modelMap' => [
				'User' => 'app\models\User',
				'Profile' => 'app\models\Profile',
			],
		],
		'gridview' =>  [
			'class' => '\kartik\grid\Module',
		],
	],
	'params' => $params,
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		'allowedIPs' => ['*']
	];

	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		'allowedIPs' => ['*']
	];
}

return $config;
