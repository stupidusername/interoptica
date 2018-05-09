<?php

use yii\db\Migration;

/**
 * Class m180509_192111_change_iva_to_exclude_iva_on_customer
 */
class m180509_192111_change_iva_to_exclude_iva_on_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->dropColumn('{{%customer}}', 'iva');
      $this->addColumn('{{%customer}}', 'exclude_iva', $this->boolean() . ' AFTER tax_situation_category');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%customer}}', 'exclude_iva');
      $this->addColumn('{{%customer}}', 'iva', $this->money() . ' AFTER tax_situation_category');
    }
}
