<?php

use yii\db\Migration;

/**
 * Class m180330_182711_add_edit_user_id_and_edit_datetime_to_issue_comment
 */
class m180330_182711_add_edit_user_id_and_edit_datetime_to_issue_comment extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->addColumn('{{%issue_comment}}', 'edit_user_id', $this->integer(). ' AFTER create_datetime');
      $this->addColumn('{{%issue_comment}}', 'edit_datetime', $this->datetime(). ' AFTER edit_user_id');

      $this->addForeignKey('{{%fk_issue_comment_edit_user}}', '{{%issue_comment}}', 'edit_user_id', '{{%user}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropForeignKey('{{%fk_issue_comment_edit_user}}', '{{%issue_comment}}');

      $this->dropColumn('{{%issue_comment}}', 'edit_user_id');
      $this->dropColumn('{{%issue_comment}}', 'edit_datetime');
    }
}
