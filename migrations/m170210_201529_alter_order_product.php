<?php

use yii\db\Migration;

class m170210_201529_alter_order_product extends Migration
{
    public function up()
    {
		$this->addColumn('{{%order_product}}', 'price', $this->money());
		$this->addColumn('{{%order_product}}', 'quantity', $this->integer());
    }

    public function down()
    {
		$this->dropColumn('{{%order_product}}', 'quantity');
		$this->dropColumn('{{%order_product}}', 'price');
    }
}
