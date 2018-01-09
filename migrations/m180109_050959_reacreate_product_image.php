<?php

use yii\db\Migration;

/**
 * Class m180109_050959_reacreate_product_image
 */
class m180109_050959_reacreate_product_image extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->dropTable('{{%product_image}}');

      $this->createTable('{{%product_image}}', [
        'id' => $this->primaryKey(),
        'type' => $this->string(),
        'ownerId' => $this->string() . ' NOT NULL',
        'rank' => $this->integer() . ' NOT NULL DEFAULT 0',
        'name' => $this->string(),
        'description' => $this->string(),
      ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropTable('{{%product_image}}');

      $this->createTable('{{%product_image}}', [
        'id' => $this->primaryKey(),
        'product_id' => $this->integer(),
        'filename' => $this->string(),
        'rank' => $this->smallInteger(),
      ]);

      $this->addForeignKey('{{%fk_product_image_product}}', '{{%product_image}}', 'product_id', '{{%product}}', 'id');
    }
}
