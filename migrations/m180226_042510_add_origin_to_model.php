<?php

use yii\db\Migration;

/**
 * Class m180226_042510_add_origin_to_model
 */
class m180226_042510_add_origin_to_model extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->addColumn('{{%model}}', 'origin', $this->smallInteger() . ' AFTER type');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%model}}', 'origin');
    }
}
