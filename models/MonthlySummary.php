<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "monthly_summary".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $begin_date
 * @property string $invoiced
 * @property integer $objective
 *
 * @property User $user
 */
class MonthlySummary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monthly_summary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'objective'], 'integer', 'min' => 0],
            [['begin_date'], 'date', 'format' => 'php: Y-m-d'],
            [['invoiced'], 'number'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
	    [['user_id', 'begin_date'], 'unique', 'targetAttribute' => ['user_id', 'begin_date']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID Usuario',
            'begin_date' => 'Fecha de Inicio',
            'invoiced' => 'Facturado',
            'objective' => 'Objetivo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Creates the models for the previous and subsequent 12 months of current date.
     */
    public static function createModels() {
	    $userIds = Yii::$app->authManager->getUserIdsByRole('salesman');
	    $midDate = strtotime(gmdate('Y-m') . '-01');
	    $months = array_map(function ($value) use ($midDate) {
		    return date('Y-m-d', strtotime("+$value month", $midDate));
	    }, range(-12, 12));
	    $models = self::find()->select(['user_id', 'begin_date'])->andWhere(['begin_date' => $months])->asArray()->all();
	    foreach ($months as $month) {
		    foreach ($userIds as $userId) {
			    if (!ArrayHelper::isIn(['user_id' => $userId, 'begin_date' => $month], $models)) {
				    $model = new self();
				    $model->user_id = $userId;
				    $model->begin_date = $month;
				    $model->save(false);
			    }
		    }
	    }
    }
}
