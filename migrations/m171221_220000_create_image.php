<?php

use yii\db\Migration;

/**
 * Class m171221_220000_product_image
 */
class m171221_220000_create_image extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->createTable('{{%image}}', [
        'id' => $this->primaryKey(),
        'product_id' => $this->integer(),
        'filename' => $this->string(),
        'rank' => $this->smallInteger(),
      ]);

      $this->addForeignKey('{{%fk_image_product}}', '{{%image}}', 'product_id', '{{%product}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%image}}');
    }
}
