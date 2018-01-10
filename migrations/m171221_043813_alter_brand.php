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
      $this->addColumn('{{%brand}}', 'logo', $this->string() . ' AFTER name');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropColumn('{{%brand}}', 'logo');
      $this->addColumn('{{%brand}}', 'gecom_code', $this->string() . ' AFTER id');
    }
}
