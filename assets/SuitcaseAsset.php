<?php

namespace app\assets;

use yii\web\AssetBundle;

class SuitcaseAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/suitcase.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
