<?php

use yii\db\Migration;

class m170209_235456_add_zone_id_to_customer extends Migration
{
    public function up()
    {
		$this->addColumn('{{%customer}}', 'zone_id', $this->integer() . ' AFTER name');
		$this->addForeignKey('{{%fk_customer_zone}}', '{{%customer}}', 'zone_id', '{{%zone}}', 'id');
    }

    public function down()
    {
		$this->dropForeignKey('{{%fk_customer_zone}}', '{{%customer}}');
        $this->dropColumn('{{%customer}}', 'zone_id');
    }
}
