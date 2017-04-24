<?php

namespace app\assets;

use yii\web\AssetBundle;

class IssueAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
		'js/issue.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
