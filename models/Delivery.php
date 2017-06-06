<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $issue_id
 * @property string $transport
 * @property integer $deleted
 *
 * @property Issue $issue
 * @property Order $order
 */
class Delivery extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'delivery';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['order_id', 'issue_id', 'deleted'], 'integer'],
			[['transport'], 'string', 'max' => 255],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
			[['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'order_id' => 'Order ID',
			'issue_id' => 'Issue ID',
			'transport' => 'Transport',
			'deleted' => 'Deleted',
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
	public function getOrder()
	{
		return $this->hasOne(Order::className(), ['id' => 'order_id']);
	}
}
