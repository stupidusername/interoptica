<?php

$params_local = require(__DIR__ . '/params_local.php');

return [
  'class' => 'app\components\afip\Afip',
  'options' => [
    'CUIT' => $params_local['cuit'],
    'cert' => '../../../../afip_cert/cert',
    'key' => '../../../../afip_cert/key',
    'production' => YII_ENV == 'prod',
  ],
];
