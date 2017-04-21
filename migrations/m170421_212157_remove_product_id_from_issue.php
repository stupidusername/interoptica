<?php

use yii\db\Migration;

class m170421_212157_remove_product_id_from_issue extends Migration
{
    public function up()
    {
		$this->dropForeignKey('{{%fk_issue_product}}', '{{%issue}}');
		$this->dropColumn('{{%issue}}', 'product_id');
    }

    public function down()
    {
		$this->addColumn('{{%issue}}', 'product_id', $this->integer() . ' AFTER order_id');
        $this->addForeignKey('{{%fk_issue_product}}', '{{%issue}}', 'product_id', '{{%product}}', 'id');
    }
}
