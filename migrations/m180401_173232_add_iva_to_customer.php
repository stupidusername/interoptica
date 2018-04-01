<?php

use yii\db\Migration;

/**
 * Class m180401_173232_add_iva_to_customer
 */
class m180401_173232_add_iva_to_customer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->addColumn('{{%customer}}', 'iva', $this->money() . ' AFTER tax_situation_category');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}', 'iva');
    }
}
