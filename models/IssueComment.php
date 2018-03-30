<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "issue_comment".
 *
 * @property integer $id
 * @property integer $issue_id
 * @property integer $user_id
 * @property string $create_datetime
 * @property integer $edit_user_id
 * @property string $edit_datetime
 * @property string $comment
 *
 * @property Issue $issue
 * @property User $user
 */
class IssueComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'issue_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['comment'], 'required'],
            [['comment'], 'string'],
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			if ($insert) {
				$this->user_id = Yii::$app->user->id;
				$this->create_datetime = gmdate('Y-m-d H:i:s');
			} else {
        $this->edit_user_id = Yii::$app->user->id;
        $this->edit_datetime = gmdate('Y-m-d H:i:s');
      }
			return true;
		} else {
			return false;
		}
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'issue_id' => 'ID Reclamo',
            'user_id' => 'ID Usuario',
            'create_datetime' => 'Fecha',
            'edit_user_id' => 'ID Usuario (Edit.)',
            'edit_datetime' => 'Editado',
            'comment' => 'Comentario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssue()
    {
        return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEditUser()
    {
        return $this->hasOne(User::className(), ['id' => 'edit_user_id']);
    }
}
