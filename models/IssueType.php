<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "issue_type".
 *
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 *
 * @property Issue[] $issues
 */
class IssueType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'issue_type';
    }

	/** @inheritdoc */
	public function behaviors() {
		return [
			'softDeleteBehavior' => [
				'class' => SoftDeleteBehavior::className(),
				'softDeleteAttributeValues' => [
					'deleted' => true
				],
				'replaceRegularDelete' => true
			],
		];
	}
	
	/** @inheritdoc */
	public static function find()
    {
        return parent::find()->where(['or', ['deleted' => null], ['deleted' => 0]]);
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['name'], 'required'],
            [['deleted'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssues()
    {
        return $this->hasMany(Issue::className(), ['issue_type_id' => 'id']);
    }
}
