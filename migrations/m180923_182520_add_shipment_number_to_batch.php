<?php

use yii\db\Migration;

/**
 * Class m180923_182520_add_shipment_number_to_batch
 */
class m180923_182520_add_shipment_number_to_batch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%batch}}', 'shipment_number', $this->string() . ' AFTER dispatch_number');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%batch}}', 'shipment_number');
    }
}
