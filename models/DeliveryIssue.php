<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery_issue".
 *
 * @property integer $id
 * @property integer $delivery_id
 * @property integer $issue_id
 *
 * @property Delivery $delivery
 * @property Issue $issue
 */
class DeliveryIssue extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'delivery_issue';
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		if ($insert) {
			$this->issue->status = Delivery::issueStatusMap()[$this->delivery->status];
			$this->issue->save();
		}
		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['delivery_id', 'issue_id'], 'integer'],
			[['delivery_id', 'issue_id'], 'required'],
			[['delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Delivery::className(), 'targetAttribute' => ['delivery_id' => 'id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
			[
				['issue_id'],
				'unique',
				'when' => function() { return count(self::find()->andWhere(['issue_id' => $this->issue_id])->innerJoinWith('delivery')); },
				'message' => 'Este reclamo ya se encuentra asociado a un envio.',
			],
			[['issue_id'], 'unique', 'targetAttribute' => ['delivery_id', 'issue_id'], 'message' => 'El reclamo ya se encuentra en el envio.'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'delivery_id' => 'Delivery ID',
			'issue_id' => 'Issue ID',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDelivery()
	{
		return $this->hasOne(Delivery::className(), ['id' => 'delivery_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue()
	{
		return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
	}
}
