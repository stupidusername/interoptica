<?php

use yii\db\Migration;

/**
 * Class m180614_082525_create_egress
 */
class m180614_082525_create_egress extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('{{%egress}}', [
        'id' => $this->primaryKey(),
        'user_id' => $this->integer(),
        'create_datetime' => $this->datetime(),
        'reason' => $this->smallInteger(),
        'comment' => $this->text(),
        'deleted' => $this->boolean(),
      ]);

      $this->addForeignKey('{{%fk_egress_user}}', '{{%egress}}', 'user_id', '{{%user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropTable('{{%egress}}');
    }
}
