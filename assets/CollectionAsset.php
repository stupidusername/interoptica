<?php

namespace app\assets;

use yii\web\AssetBundle;

class CollectionAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/collection.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
