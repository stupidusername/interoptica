<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "fail".
 *
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 *
 * @property IssueProduct[] $issueProducts
 */
class Fail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fail';
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
        return parent::find()->where(['or', [self::tableName() . '.deleted' => null], [self::tableName() . '.deleted' => 0]]);
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['name'], 'required'], 
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
            'name' => 'Name',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssueProducts()
    {
        return $this->hasMany(IssueProduct::className(), ['fail_id' => 'id']);
    }
	
	/**
	 * Returns an array containing the names of all undeleted product fails.
	 * @return string[]
	 */
	public static function getIdNameArray() {
		$fails = self::find()->select(['id', 'name'])->asArray()->all();
		return ArrayHelper::map($fails, 'id', 'name');
	}
}
