<?php

namespace app\assets;

use yii\web\AssetBundle;

class CustomerViewAsset extends AssetBundle {

	public $basePath = '@webroot';
	public $baseUrl = '@web';
	
	public $js = [
		'js/customer-view.js',
	];
	
	public $depends = [
        'app\assets\AppAsset',
    ];
}
