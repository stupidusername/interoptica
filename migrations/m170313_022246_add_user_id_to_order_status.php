<?php

use yii\db\Migration;

class m170313_022246_add_user_id_to_order_status extends Migration
{
    public function up()
    {
		$this->addColumn('{{%order_status}}', 'user_id', $this->integer());
		$this->addForeignKey('{{%fk_order_status_user}}', '{{%order_status}}', 'user_id', '{{%user}}', 'id');
    }

    public function down()
    {
		$this->dropForeignKey('{{%fk_order_status_user}}', '{{%order_status}}');
		$this->dropColumn('{{%order_status}}', 'user_id');
    }
}
