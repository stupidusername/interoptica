<?php

use yii\db\Migration;

class m170518_204657_alter_issue_type extends Migration
{
    public function up()
    {
		$this->addColumn('{{%issue_type}}', 'required_issue_product_fail_id', $this->boolean() . ' AFTER required_issue_product');
		$this->addColumn('{{%issue_type}}', 'required_issue_product_quantity', $this->boolean() . ' AFTER required_issue_product_fail_id');
    }

    public function down()
    {
		$this->dropColumn('{{%issue_type}}', 'required_issue_product_quantity');
		$this->dropColumn('{{%issue_type}}', 'required_issue_product_fail_id');
    }
}
