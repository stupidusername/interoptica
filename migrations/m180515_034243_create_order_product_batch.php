<?php

use yii\db\Migration;

/**
 * Class m180515_034243_create_order_product_batch
 */
class m180515_034243_create_order_product_batch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('{{%order_product_batch}}', [
        'id' => $this->primaryKey(),
        'order_product_id' => $this->integer(),
        'batch_id' => $this->integer(),
        'quantity' => $this->integer(),
      ]);

      $this->addForeignKey('{{%fk_order_product_batch_order_product}}', '{{%order_product_batch}}', 'order_product_id', '{{%order_product}}', 'id');
      $this->addForeignKey('{{%fk_order_product_batch_batch}}', '{{%order_product_batch}}', 'batch_id', '{{%batch}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_product_batch}}');
    }
}
