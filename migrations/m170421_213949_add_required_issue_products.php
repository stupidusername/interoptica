<?php

use yii\db\Migration;

class m170421_213949_add_required_issue_products extends Migration
{
    public function up()
    {
		$this->addColumn('{{%issue_type}}', 'required_issue_product', $this->boolean() . ' AFTER name');
    }

    public function down()
    {
		$this->dropColumn('{{%issue_type}}', 'required_issue_product');
    }
}
