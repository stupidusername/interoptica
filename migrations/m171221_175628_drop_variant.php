<?php

use yii\db\Migration;

/**
 * Class m171221_175628_drop_product_variant
 */
class m171221_175628_drop_variant extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->dropForeignKey('{{%fk_product_variant}}', '{{%product}}');
      $this->dropTable('{{%variant}}');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->createTable('{{%variant}}', [
          'id' => $this->primaryKey(),
          'gecom_code' => $this->string(),
          'name' => $this->string(),
          'model_id' => $this->integer(),
      ]);

      $this->createIndex('{{%variant_unique_gecom_code}}', '{{%variant}}', 'gecom_code', true);
      $this->addForeignKey('{{%fk_variant_model}}', '{{%variant}}', 'model_id', '{{%model}}', 'id');
      $this->addForeignKey('{{%fk_product_variant}}', '{{%product}}', 'variant_id', '{{%variant}}', 'id');
    }
}
