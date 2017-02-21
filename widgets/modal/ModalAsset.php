<?php

namespace app\widgets\modal;

class ModalAsset extends \yii\web\AssetBundle {
    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/widgets/modal/assets/';

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/kb-modal-ajax.js',
    ];
}
