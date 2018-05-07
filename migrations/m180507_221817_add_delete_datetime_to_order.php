<?php

use app\models\Order;
use yii\db\Migration;

/**
 * Class m180507_221817_add_delete_datetime_to_order
 */
class m180507_221817_add_delete_datetime_to_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%order}}', 'delete_datetime', $this->datetime());
      // Pupulate deleted time
      foreach(Order::find()->where(['and', ['not', ['deleted' => null]], ['!=', 'deleted', 0]])->with('orderStatus')->each() as $order) {
        $order->delete_datetime = $order->orderStatus->create_datetime;
        $order->save(false);
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'delete_datetime');
    }
}
