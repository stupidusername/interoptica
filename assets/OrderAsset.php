<?php

namespace app\assets;

use yii\web\AssetBundle;

class OrderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/order.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
