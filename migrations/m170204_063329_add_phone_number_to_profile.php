<?php

use yii\db\Migration;

class m170204_063329_add_phone_number_to_profile extends Migration {

	public function up() {
		$this->addColumn('{{%profile}}', 'phone_number', $this->string() . ' AFTER public_email');
	}

	public function down() {
		$this->dropColumn('{{%profile}}', 'phone_number');
	}

}
