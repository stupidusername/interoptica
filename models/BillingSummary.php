<?php

namespace app\models;

use app\helpers\DateHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BillingSummary extends MonthlySummary {

	/**
	 * @var string
	 */
	public $period;

	/**
	 * @var string
	 */
	public $fromDate;

	/**
	 * @var string
	 */
	public $toDate;

	/**
	 * @var string
	 */
	public $queryFromDate;

	/**
	 * @var string
	 */
	public $queryToDate;

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		$this->fromDate = Yii::$app->formatter->asDate(strtotime('-1 month'), 'yyyy-MM-dd');
		$this->toDate = Yii::$app->formatter->asDate(time(), 'yyyy-MM-dd');
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['fromDate', 'toDate'], 'required'],
			[['fromDate', 'toDate'], 'string'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'fromDate' => 'Desde',
			'toDate' => 'Hasta',
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$this->load($params);

		$this->queryFromDate = DateHelper::currentMonth($this->fromDate);
		$this->queryToDate = DateHelper::nextMonth($this->toDate);

		$query = self::find()->select(['user_id', 'invoiced', 'period' => 'SUBDATE(begin_date, DAYOFMONTH(begin_date) - 1)'])
			->andWhere(['>=', 'begin_date', $this->queryFromDate])->andWhere(['<', 'begin_date', $this->queryToDate])
			->indexBy(function ($row) { return $row['user_id'] . '-' . $row['period']; });

		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->innerJoinWith(['user']);

		return $dataProvider;
	}
}
