<?php

use yii\db\Migration;

/**
 * Class m180408_234636_add_iva_to_order
 */
class m180408_234636_add_iva_to_order extends Migration
{
  /**
   * @inheritdoc
   */
  public function safeUp()
  {
    $this->addColumn('{{%order}}', 'iva', $this->money() . ' AFTER transport_id');
  }

  /**
   * @inheritdoc
   */
  public function safeDown()
  {
      $this->dropColumn('{{%order}}', 'iva');
  }
}
