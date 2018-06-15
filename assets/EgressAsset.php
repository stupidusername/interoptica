<?php

namespace app\assets;

use yii\web\AssetBundle;

class EgressAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/egress.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
