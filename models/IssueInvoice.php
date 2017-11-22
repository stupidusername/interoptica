<?php

namespace app\models;

use app\validators\InvoiceValidator;
use Yii;

/**
 * This is the model class for table "issue_invoice".
 *
 * @property integer $id
 * @property integer $issue_id
 * @property string $number
 * @property string $comment
 *
 * @property Issue $issue
 */
class IssueInvoice extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'issue_invoice';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['issue_id', 'number'], 'required'],
			[['issue_id'], 'integer'],
			[['number'], 'unique'],
			[['number'], InvoiceValidator::className()],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
			[['comment'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'issue_id' => 'ID Reclamo',
			'number' => 'Número de Factura',
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
}
