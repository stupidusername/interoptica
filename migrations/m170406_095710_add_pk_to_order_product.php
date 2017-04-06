<?php

use yii\db\Migration;

class m170406_095710_add_pk_to_order_product extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
		$this->createIndex('{{%order_product_unique_order_id_product_id}}', '{{%order_product}}', ['order_id', 'product_id'], true);
        $this->dropPrimaryKey('{{%pk_order_product}}', '{{%order_product}}');
		$this->addColumn('{{%order_product}}', 'id', $this->primaryKey() . ' FIRST');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
		$this->dropColumn('{{%order_product}}', 'id');
		$this->addPrimaryKey('{{%pk_order_product}}', '{{%order_product}}', ['order_id', 'product_id']);
		$this->dropIndex('{{%order_product_unique_order_id_product_id}}', '{{%order_product}}');
    }
}
