<?php

use yii\db\Migration;

/**
 * Class m180330_151017_add_discount_to_customer
 */
class m180330_151017_add_discount_to_customer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->addColumn('{{%customer}}', 'discount_percentage', $this->money(). ' AFTER email');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}', 'discount_percentage');
    }
}
