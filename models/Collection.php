<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "collection".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $deleted
 *
 * @property CollectionProduct[] $collectionProducts
 */
class Collection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'collection';
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
            [['name'], 'string', 'max' => 255],
            [['name'], 'required'],
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
    public function getCollectionProducts()
    {
        return $this->hasMany(CollectionProduct::className(), ['collection_id' => 'id']);
    }
}
