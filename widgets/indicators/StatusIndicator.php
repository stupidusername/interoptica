<?php

namespace app\widgets\indicators;

use yii\base\Widget;

class StatusIndicator extends Widget {
	
	public $color;
	
	/**
	 * @inheritdoc
	 */
	public function run() {
		return '<span style="display: inline-block; width: 10px; height: 10px; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; background: '. $this->color .';"></span>';
	}
}
