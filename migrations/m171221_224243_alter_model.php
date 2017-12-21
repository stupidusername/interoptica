<?php

use yii\db\Migration;

/**
 * Class m171221_224243_alter_model
 */
class m171221_224243_alter_model extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->alterColumn('{{%model}}', 'brand_id', $this->integer() . ' AFTER type');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('{{%model}}', 'brand_id', $this->integer() . ' AFTER mirrored');
    }
}
