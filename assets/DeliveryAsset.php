<?php

namespace app\assets;

use yii\web\AssetBundle;

class DeliveryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/delivery.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
