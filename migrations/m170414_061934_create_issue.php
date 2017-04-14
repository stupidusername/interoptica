<?php

use yii\db\Migration;

class m170414_061934_create_issue extends Migration
{
    public function up()
    {
		$this->createTable('{{%issue}}', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer(),
			'customer_id' => $this->integer(),
			'order_id' => $this->integer(),
			'product_id' => $this->integer(),
			'issue_type_id' => $this->integer(),
			'comment' => $this->text(),
			'contact' => $this->string(),
			'deleted' => $this->boolean(),
		]);
		
		$this->addForeignKey('{{%fk_issue_user}}', '{{%issue}}', 'user_id', '{{%user}}', 'id');
		$this->addForeignKey('{{%fk_issue_customer}}', '{{%issue}}', 'customer_id', '{{%customer}}', 'id');
		$this->addForeignKey('{{%fk_issue_order}}', '{{%issue}}', 'order_id', '{{%order}}', 'id');
		$this->addForeignKey('{{%fk_issue_product}}', '{{%issue}}', 'product_id', '{{%product}}', 'id');
		$this->addForeignKey('{{%fk_issue_type}}', '{{%issue}}', 'issue_type_id', '{{%issue_type}}', 'id');
    }

    public function down()
    {
		$this->dropTable('{{%issue}}');
    }
}
