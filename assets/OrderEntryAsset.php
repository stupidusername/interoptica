<?php

namespace app\assets;

use yii\web\AssetBundle;

class OrderEntryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/order-entry.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
