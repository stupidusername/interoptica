<?php

namespace app\models;

use app\components\DeletedQuery;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $logo
 * @property boolean $deleted
 *
 * @property Model[] $models
 */
class Brand extends \yii\db\ActiveRecord
{

  const LOGO_FOLDER = '/images/brand/';

  /**
  * @var UploadedFile
  */
  public $logoUpload;

  /**
  * @var boolean
  */
  public $deleteLogo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brand';
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

    /**
    * @inheritdoc
    */
    public static function find() {
      return new DeletedQuery(get_called_class());
    }

    /**
    * @inheritdoc
    */
    public function beforeSave($insert) {
      if (!parent::beforeSave($insert)) {
        return false;
      }
      $logoUpload = UploadedFile::getInstance($this, 'logoUpload');
      if (!empty($logoUpload)) {
        $filename = uniqid() . '.' . $logoUpload->extension;
        $logoUpload->saveAs(Yii::getAlias('@webroot' . self::LOGO_FOLDER . $filename));
        $this->logo = $filename;
      } else if ($this->deleteLogo) {
        $this->logo = null;
      }
      return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['name'], 'required'],
            [['logoUpload'], 'image', 'extensions' => 'jpg, png', 'maxSize' => 1024 * 1024],
            [['deleteLogo'], 'integer'],
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
            'logo' => 'Logo',
            'logoUpload' => 'Logo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModels()
    {
        return $this->hasMany(Model::className(), ['brand_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuitcases()
    {
        return $this->hasMany(Suitcase::className(), ['id' => 'suitcase_id'])->viaTable('suitcase_brand', ['brand_id' => 'id']);
    }

    /**
    * @return string|null
    */
    public function getLogoUrl() {
      $url = null;
      if ($this->logo) {
        $url = Yii::$app->imageCache->thumbSrc(self::LOGO_FOLDER . $this->logo);
      }
      return $url;
    }

    /**
  	 * Gets an id => name array.
  	 * return string[]
  	 */
  	public static function getIdNameArray() {
  		$brands = ArrayHelper::map(self::find()->select(['id', 'name'])->active()->asArray()->all(), 'id', 'name');
  		return $brands;
  	}
}
