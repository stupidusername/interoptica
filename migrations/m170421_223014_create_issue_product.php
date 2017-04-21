<?php

use yii\db\Migration;

class m170421_223014_create_issue_product extends Migration
{
    public function up()
    {
		$this->createTable('{{%issue_product}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer(),
			'product_id' => $this->integer(),
			'fail_id' => $this->integer(),
			'quantity' => $this->integer(),
			'comment' => $this->text(),
		]);
		
		$this->addForeignKey('{{%fk_issue_product_issue}}', '{{%issue_product}}', 'issue_id', '{{%issue}}', 'id');
		$this->addForeignKey('{{%fk_issue_product_product}}', '{{%issue_product}}', 'product_id', '{{%product}}', 'id');
		$this->addForeignKey('{{%fk_issue_product_fail}}', '{{%issue_product}}', 'fail_id', '{{%fail}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%issue_product}}');
    }
}
