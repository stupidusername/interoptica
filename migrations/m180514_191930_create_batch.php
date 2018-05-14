<?php

use yii\db\Migration;

/**
 * Class m180514_191930_create_batch
 */
class m180514_191930_create_batch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('{{%batch}}', [
        'id' => $this->primaryKey(),
        'product_id' => $this->integer(),
        'entered_date' => $this->date(),
        'dispatch_number' => $this->string(),
        'initial_stamp_numer' => $this->integer(),
        'quantity' => $this->integer(),
        'stock' => $this->integer(),
      ]);

      $this->addForeignKey('{{%fk_batch_product}}', '{{%batch}}', 'product_id', '{{%product}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%batch}}');
    }
}
