<?php

use yii\db\Migration;

/**
 * Class m180614_082555_create_egress_product_batch
 */
class m180614_082555_create_egress_product_batch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('{{%egress_product_batch}}', [
        'id' => $this->integer(),
        'egress_product_id' => $this->integer(),
        'batch_id' => $this->integer(),
        'quantity' => $this->integer(),
      ]);

      $this->addForeignKey('{{%fk_egress_product_batch_egress_product}}', '{{%egress_product_batch}}', 'egress_product_id', '{{%egress_product}}', 'id');
      $this->addForeignKey('{{%fk_egress_product_batch_batch}}', '{{%egress_product_batch}}', 'batch_id', '{{%batch}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%egress_product_batch}}');
    }
}
