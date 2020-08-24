<?php

use app\models\Customer;
use yii\db\Migration;

/**
 * Class m180507_221817_add_delete_datetime_to_order
 */
class m200823_193845_alter_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%customer}}', 'create_datetime', $this->datetime() . ' AFTER cuit');
      $this->addColumn('{{%customer}}', 'update_datetime', $this->datetime() . ' AFTER create_datetime');
      // Pupulate deleted time
      foreach(Customer::find()->where([])->each() as $customer) {
        $customer->create_datetime = '2017-04-01 00:00:0';
        $customer->save(false);
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}', 'create_datetime');
        $this->dropColumn('{{%customer}}', 'update_datetime');
    }
}
