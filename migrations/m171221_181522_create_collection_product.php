<?php

use yii\db\Migration;

/**
 * Class m171221_181522_create_collection_product
 */
class m171221_181522_create_collection_product extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->createTable('{{%collection_product}}', [
        'collection_id' => $this->integer(),
        'product_id' => $this->integer(),
      ]);
      $this->addForeignKey('{{%fk_collection_product_collection}}', '{{%collection_product}}', 'collection_id', '{{%collection}}', 'id');
      $this->addForeignKey('{{%fk_collection_product_product}}', '{{%collection_product}}', 'product_id', '{{%product}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%collection_product}}');
    }
}
