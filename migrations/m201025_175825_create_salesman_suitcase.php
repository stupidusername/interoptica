<?php

use yii\db\Schema;
use yii\db\Migration;

class m201025_175825_create_salesman_suitcase extends Migration
{
    public function up() {
		$this->createTable('{{%salesman_suitcase}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'customer_id' => $this->integer(),
            'suitcase_id' => $this->integer(),
        ]);

        $this->addForeignKey('{{%fk_salesman_suitcase_user}}', '{{%salesman_suitcase}}', 'user_id', '{{%user}}', 'id');
        $this->addForeignKey('{{%fk_salesman_suitcase_customer}}', '{{%salesman_suitcase}}', 'customer_id', '{{%customer}}', 'id');
        $this->addForeignKey('{{%fk_salesman_suitcase_suitcase}}', '{{%salesman_suitcase}}', 'suitcase_id', '{{%suitcase}}', 'id');
	}

	public function down() {
        $this->dropForeignKey('{{%fk_salesman_suitcase_user}}', '{{%salesman_suitcase}}');
        $this->dropForeignKey('{{%fk_salesman_suitcase_customer}}', '{{%salesman_suitcase}}');
        $this->dropForeignKey('{{%fk_salesman_suitcase_suitcase}}', '{{%salesman_suitcase}}');
		$this->dropTable('{{%salesman_suitcase}}');
	}
}
