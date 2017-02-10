<?php

use yii\db\Migration;

class m170210_202034_create_order_status extends Migration
{
    public function up()
    {
		$this->createTable('{{%order_status}}', [
			'id' => $this->primaryKey(),
			'order_id' => $this->integer(),
			'status' => $this->smallInteger(),
			'create_datetime' => $this->dateTime(),
		]);
		
		$this->addForeignKey('{{%fk_status_order}}', '{{%order_status}}', 'order_id', '{{%order}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%order_status}}');
    }
}
