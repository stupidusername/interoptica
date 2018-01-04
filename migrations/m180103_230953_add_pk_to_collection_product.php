<?php

use yii\db\Migration;

/**
 * Class m180103_230953_add_pk_to_collection_product
 */
class m180103_230953_add_pk_to_collection_product extends Migration
{
  /**
   * @inheritdoc
   */
  public function safeUp()
  {
    $this->createIndex('{{%collection_product_unique_collection_id_product_id}}', '{{%collection_product}}', ['collection_id', 'product_id'], true);
    $this->addColumn('{{%collection_product}}', 'id', $this->primaryKey() . ' FIRST');
  }

  /**
   * @inheritdoc
   */
  public function safeDown()
  {
    $this->dropColumn('{{%collection_product}}', 'id');
    $this->dropIndex('{{%collection_product_unique_collection_id_product_id}}', '{{%collection_product}}');
  }
}
