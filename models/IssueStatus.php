<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "issue_status".
 *
 * @property integer $id
 * @property integer $issue_id
 * @property integer $user_id
 * @property integer $status
 * @property string $create_datetime
 *
 * @property Issue $issue
 */
class IssueStatus extends \yii\db\ActiveRecord
{
	const STATUS_OPEN = 0;
	const STATUS_CLOSED = 1;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'issue_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['issue_id', 'status'], 'integer'],
            [['create_datetime'], 'safe'],
            [['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'issue_id' => 'ID Pedido',
			'user_id' => 'ID Usuario',
            'status' => 'Estado',
            'create_datetime' => 'Fecha',
        ];
    }

	/**
	 * Status labels
	 * @return string[]
	 */
	public static function statusLabels() {
		return [
			self::STATUS_OPEN => 'Abierto',
			self::STATUS_CLOSED => 'Cerrado',
		];
	}
	
	/**
	 * @return string
	 */
	public function getStatusLabel() {
		return self::statusLabels()[$this->status];
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
	 * Get last status of each issue
	 * @return \yii\db\ActiveQuery
	 */
	public static function getLastStatuses() {
		return self::find()->select(['id' => 'max(id)'])->asArray()->groupBy('issue_id');
	}
}
