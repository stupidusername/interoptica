<?php

use yii\db\Migration;

/**
 * Class m180515_035108_drop_quantity_from_order_product
 */
class m180515_035108_drop_quantity_from_order_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->dropColumn('{{%order_product}}', 'quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%order_product}}', 'quantity', $this->integer() . ' AFTER price');
    }
}
