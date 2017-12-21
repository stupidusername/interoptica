<?php

use yii\db\Migration;

/**
 * Class m171221_043813_create_brand
 */
class m171221_043813_alter_brand extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->dropColumn('{{%brand}}', 'gecom_code');
      $this->addColumn('{{%brand}}', 'logo', $this->string());
      $this->addColumn('{{%brand}}', 'deleted', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropColumn('{{%brand}}', 'logo');
      $this->dropColumn('{{%brand}}', 'deleted');
      $this->addColumn('{{%brand}}', 'gecom_code', $this->string() . ' AFTER id');
    }
}
