<?php

use yii\db\Migration;

/**
 * Class m171221_214210_alter_product
 */
class m171221_214210_alter_product extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->dropColumn('{{%product}}', 'variant_id');
      $this->dropColumn('{{%product}}', 'gecom_desc');
      $this->dropColumn('{{%product}}', 'extra');
      $this->renameColumn('{{%product}}', 'gecom_code', 'code');
      $this->addColumn('{{%product}}', 'model_id', $this->integer() . ' AFTER id');
      $this->addColumn('{{%product}}', 'create_date', $this->date() . ' AFTER running_low_date');
      $this->addColumn('{{%product}}', 'update_date', $this->date() . ' AFTER create_date');
      $this->addForeignKey('{{%fk_product_model}}', '{{%product}}', 'model_id', 'model', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropForeignKey('{{%fk_product_model}}', '{{%product}}');
      $this->dropColumn('{{%product}}', 'model_id');
      $this->dropColumn('{{%product}}', 'create_date');
      $this->dropColumn('{{%product}}', 'update_date');
      $this->renameColumn('{{%product}}', 'code', 'gecom_code');
      $this->addColumn('{{%product}}', 'variant_id', $this->integer() . ' AFTER id');
      $this->addColumn('{{%product}}', 'gecom_desc', $this->string() . ' AFTER gecom_code');
      $this->addColumn('{{%product}}', 'extra', $this->string() . ' AFTER running_low_date');
    }
}
