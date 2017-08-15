<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderSummary extends Order {

	const PERIOD_WEEK = 0;
	const PERIOD_MONTH = 1;
	const PERIOD_YEAR = 2;

	/**
	 * @var integer
	 */
	public $totalQuantity;

	/**
	 * @var integer
	 */
	public $week;

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
	 * @var integer
	 */
	public $period;

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		$this->fromDate = Yii::$app->formatter->asDate(strtotime('-1 month'), 'yyyy-MM-dd');
		$this->toDate = Yii::$app->formatter->asDate(time(), 'yyyy-MM-dd');
		$this->period = self::PERIOD_WEEK;
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['fromDate', 'toDate', 'period'], 'required'],
			[['fromDate', 'toDate'], 'string'],
			[['period'], 'integer'],
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
			'period' => 'Periodo',
		];
	}

	/**
	 * @return string
	 */
	public static function periodLabels() {
		return [
			self::PERIOD_WEEK => 'Semanal',
			self::PERIOD_MONTH => 'Mensual',
			self::PERIOD_YEAR => 'Anual',
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

		$userIds = Yii::$app->authManager->getUserIdsByRole('salesman');
		$sqlPeriodFunctions = [
			self::PERIOD_WEEK => 'SUBDATE(DATE(order_status.create_datetime), WEEKDAY(order_status.create_datetime))',
			self::PERIOD_MONTH => 'SUBDATE(DATE(order_status.create_datetime), DAYOFMONTH(order_status.create_datetime) - 1)',
			self::PERIOD_YEAR => 'SUBDATE(DATE(order_status.create_datetime), DAYOFYEAR(order_status.create_datetime) - 1)',
		];
		$query = self::find()->select(['order.user_id', 'totalQuantity' => 'SUM(order_product.quantity)', 'period' => $sqlPeriodFunctions[$this->period]])
			->andWhere(['order.user_id' => $userIds])->with(['orderStatus'])->groupBy(['order.user_id', 'period'])->indexBy(function ($row) { return $row['user_id'] . '-' . $row['period']; });

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->innerJoinWith([
			'user',
			'enteredOrderStatus' => function ($query) {
				if ($this->fromDate) {
					switch ($this->period) {
					case self::PERIOD_WEEK:
						$this->queryFromDate = gmdate('Y-m-d', strtotime('monday next week -7 days' . $this->fromDate));
						break;
					case self::PERIOD_MONTH:
						$this->queryFromDate = gmdate('Y-m-01', strtotime($this->fromDate));
						break;
					case self::PERIOD_YEAR:
						$this->queryFromDate = gmdate('Y-01-01', strtotime($this->fromDate));
						break;
					}
					$query->andWhere(['>=', 'create_datetime', $this->queryFromDate]);
				}
				if ($this->toDate) {
					switch ($this->period) {
					case self::PERIOD_WEEK:
						$this->queryToDate = gmdate('Y-m-d', strtotime('monday next week ' . $this->toDate));
						break;
					case self::PERIOD_MONTH:
						$this->queryToDate = gmdate('Y-m-01', strtotime('+1 month ' . $this->toDate));
						break;
					case self::PERIOD_YEAR:
						$this->queryToDate = gmdate('Y-01-01', strtotime('+1 year ' . $this->toDate));
						break;
					}
					$query->andWhere(['<', 'create_datetime', $this->queryToDate]);
				}
			},
			'orderProducts']);

		return $dataProvider;
	}
}
