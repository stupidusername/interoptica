<?php

namespace app\models;

use app\components\DeletedQuery;
use Yii;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "suitcase".
 *
 * @property int $id
 * @property string $name
 * @property int $deleted
 */
class Suitcase extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suitcase';
    }

    /**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return [
			'softDeleteBehavior' => [
				'class' => SoftDeleteBehavior::className(),
				'softDeleteAttributeValues' => [
					'deleted' => true,
				],
				'replaceRegularDelete' => true
			],
		];
	}
    /**
	* @inheritdoc
	*/
	public static function find() {
		return new DeletedQuery(get_called_class());
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function getSuitcaseBrands()
    {
        return $this->hasMany(SuitcaseBrand::className(), ['suitcase_id' => 'id']);
    }

    /**
	 * Gets an id => name array.
	 * @return string[]
	 */
	public static function getIdNameArray() {
		$suitcases = ArrayHelper::map(self::find()->active()->select(['id', 'name'])->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
		return $suitcases;
	}
}
