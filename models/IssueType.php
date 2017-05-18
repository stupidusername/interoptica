<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "issue_type".
 *
 * @property integer $id
 * @property string $name
 * @property integer $required_issue_product
 * @property integer $required_issue_product_fail_id
 * @property integer $required_issue_product_quantity
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
        return parent::find()->where(['or', [self::tableName() . '.deleted' => null], [self::tableName() . '.deleted' => 0]]);
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['name'], 'required'],
            [['required_issue_product', 'required_issue_product_fail_id', 'required_issue_product_quantity'], 'integer'],
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
			'required_issue_product' => 'Requerir Productos',
			'required_issue_product_fail_id' => 'Requerir Falla',
			'required_issue_product_quantity' => 'Requerir Cantidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssues()
    {
        return $this->hasMany(Issue::className(), ['issue_type_id' => 'id']);
    }
	
	/**
	 * Returns an array containing the names of all undeleted issue types.
	 * @return string[]
	 */
	public static function getIdNameArray() {
		$issues = self::find()->select(['id', 'name'])->asArray()->all();
		return ArrayHelper::map($issues, 'id', 'name');
	}
}
