<?php

use yii\db\Schema;
use yii\db\Migration;

class m201017_164612_add_address_to_order extends Migration
{
    public function up() {
		$this->addColumn('{{%order}}', 'delivery_address', $this->string() . ' AFTER comment');
        $this->addColumn('{{%order}}', 'delivery_city', $this->string() . ' AFTER delivery_address');
        $this->addColumn('{{%order}}', 'delivery_state', $this->string() . ' AFTER delivery_city');
        $this->addColumn('{{%order}}', 'delivery_zip_code', $this->string() . ' AFTER delivery_state');
	}

	public function down() {
		$this->dropColumn('{{%order}}', 'delivery_address');
        $this->dropColumn('{{%order}}', 'delivery_city');
        $this->dropColumn('{{%order}}', 'delivery_state');
        $this->dropColumn('{{%order}}', 'delivery_zip_code');
	}
}
