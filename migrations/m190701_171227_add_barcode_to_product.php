<?php

use yii\db\Migration;

/**
 * Class m190701_171227_add_barcode_to_product
 */
class m190701_171227_add_barcode_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'barcode', $this->string() . ' AFTER code');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'barcode');
    }
}
