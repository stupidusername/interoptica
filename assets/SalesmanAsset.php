<?php

namespace app\assets;

use yii\web\AssetBundle;

class SalesmanAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/salesman.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
