<?php

namespace app\assets;

use yii\web\AssetBundle;

class ProductIndexAsset extends AssetBundle {

	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $js = [
		'js/product-index.js',
	];

	public $depends = [
        'app\assets\AppAsset',
    ];
}
