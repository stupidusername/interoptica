<?php

use yii\db\Migration;

class m170210_201010_alter_order extends Migration
{
    public function up()
    {
		$this->dropColumn('{{%order}}', 'status');
		$this->addColumn('{{%order}}', 'discount_percentage', $this->money() . ' AFTER customer_id');
		$this->addColumn('{{%order}}', 'comment', $this->text() . ' AFTER discount_percentage');
    }

    public function down()
    {
		$this->dropColumn('{{%order}}', 'discount_percentage');
		$this->dropColumn('{{%order}}', 'comment');
		$this->addColumn('{{%order}}', 'status', $this->smallInteger() . ' AFTER customer_id');
    }
}
