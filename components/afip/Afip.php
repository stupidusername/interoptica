<?php

namespace app\components\afip;

use yii\base\BaseObject;

class Afip extends BaseObject {

  public $options;

  private $_afip;

  public function init() {
    parent::init();
    include_once 'src/Afip.php';
    $this->_afip = new \Afip($this->options);
  }

  public function __get($property)
	{
		if (in_array($property, $this->_afip->implemented_ws)) {
			return $this->_afip->{$property};
    } else {
      return parent::__get($property);
    }
	}
}
