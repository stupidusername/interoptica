<?php

$local = require(__DIR__ . '/local.php');
$params = require(__DIR__ . '/params.php');
$aliases = require(__DIR__ . '/aliases.php');
$mailer = require(__DIR__ . '/mailer.php');

$config = [
	'id' => 'basic',
	'name' => 'Interoptica',
	'language' => 'es-AR',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'timeZone' => 'America/Argentina/Buenos_Aires',
	'components' => [
		'request' => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => 'vhAlxUomdeaZU3IhHoMiWUOsNETOBg3F',
		],
		'cache' => [
			'class' => YII_ENV == 'dev' ? 'yii\caching\DummyCache' : 'yii\caching\MemCache',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'mailer' => $mailer,
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
					[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'db' => $local['db'],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
				'image/thumb/<path:.*>' => 'image/thumb',
				'docs/<view>' => 'docs/view',
				[
					'class' => 'yii\rest\UrlRule',
					'controller' =>  ['api/v1/products' => 'api/product'],
					'patterns' => [
						'GET' => 'list',
					],
				],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' =>  ['api/v1/orders' => 'api/order'],
					'patterns' => [
						'GET' => 'list',
						'GET <id>' => 'get',
					],
				],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' =>  ['api/v1/transports' => 'api/transport'],
					'patterns' => [
						'GET' => 'list',
					],
				],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' =>  ['api/v1/order-conditions' => 'api/order-condition'],
					'patterns' => [
						'GET' => 'list',
					],
				],
			],
		],
		'assetManager' => [
			'linkAssets' => true,
			'appendTimestamp' => true,
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
			'numberFormatterOptions' => [
				NumberFormatter::MIN_FRACTION_DIGITS => 2,
				NumberFormatter::MAX_FRACTION_DIGITS => 2,
			],
		],
		'imageCache' => [
			'class' => 'iutbay\yii2imagecache\ImageCache',
			'sourcePath' => '@webroot/images',
			'sourceUrl' => '@web/images',
			'thumbsPath' => '@webroot/images/thumb',
			'thumbsUrl' => '@web/image/thumb',
			'resizeMode' => 'inset',
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
			'adminPermission' => 'admin',
			'modelMap' => [
				'User' => 'app\models\User',
				'UserSearch' => 'app\models\UserSearch',
				'Profile' => 'app\models\Profile',
			],
		],
		'rbac' => 'dektrium\rbac\RbacWebModule',
		'gridview' =>  [
			'class' => '\kartik\grid\Module',
		],
	],
	'params' => $params,
	'aliases' => $aliases,
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

// Disable Excel 95 and PDF export
\Yii::$container->set('kartik\export\ExportMenu', [
	'batchSize' => 500,
	'exportConfig' => [
		'Pdf' => false,
		'Xls' => false,
	],
]);

// Change Pjax timeout
\Yii::$container->set('yii\widgets\Pjax', [
	'timeout' => 30000,
]);

return $config;
