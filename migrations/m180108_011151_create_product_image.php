<?php

use yii\db\Migration;

/**
 * Class m180108_011151_create_product_image
 */
class m180108_011151_create_product_image extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->createTable('{{%product_image}}', [
        'id' => $this->primaryKey(),
        'product_id' => $this->integer(),
        'filename' => $this->string(),
        'rank' => $this->smallInteger(),
      ]);

      $this->addForeignKey('{{%fk_product_image_product}}', '{{%product_image}}', 'product_id', '{{%product}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_image}}');
    }
}
