<?php

use yii\db\Schema;
use yii\db\Migration;

class m201023_215457_deleted_reason_to_order extends Migration
{
    public function up() {
		$this->addColumn('{{%order}}', 'delete_reason', $this->string() . ' AFTER deleted');
	}

	public function down() {
		$this->dropColumn('{{%order}}', 'delete_reason');
	}
}
