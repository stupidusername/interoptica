<?php

use yii\db\Migration;

/**
 * Class m180614_082550_create_egress_product
 */
class m180614_082550_create_egress_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('{{%egress_product}}', [
        'id' => $this->primaryKey(),
        'egress_id' => $this->integer(),
        'product_id' => $this->integer(),
      ]);

      $this->addForeignKey('{{%fk_egress_product_egress}}', '{{%egress_product}}', 'egress_id', '{{%egress}}', 'id');
      $this->addForeignKey('{{%fk_egress_product_product}}', '{{%egress_product}}', 'product_id', '{{%product}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropTable('{{%egress_product}}');
    }
}
