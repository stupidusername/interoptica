<?php

namespace app\widgets\modal;

class Modal extends \yii\bootstrap\Modal {

    /**
     * The url to request when modal is opened
     * @var string
     */
    public $url;

    /**
     * @inheritdocs
     */
    public function run()
    {
        $view = $this->getView();
        parent::run();

        ModalAsset::register($view);

        $id = $this->options['id'];
        $js = <<<JS
        jQuery('#$id').kbModalAjax({
            url: '{$this->url}',
        });
JS;
        $view->registerJs($js);
    }
}
