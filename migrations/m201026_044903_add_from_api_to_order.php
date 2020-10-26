<?php

use yii\db\Schema;
use yii\db\Migration;

class m201026_044903_add_from_api_to_order extends Migration
{
    public function up() {
		$this->addColumn('{{%order}}', 'from_api', $this->boolean() . ' AFTER user_id');
	}

	public function down() {
        $this->dropColumn('{{%order}}', 'from_api');
	}
}
