<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderSummary extends Order {

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
		$userIds = Yii::$app->authManager->getUserIdsByRole('salesman');
		$query = self::find()->select(['order.user_id', 'totalQuantity' => 'SUM(order_product.quantity)', 'week' => 'SUBDATE(DATE(order_status.create_datetime), WEEKDAY(order_status.create_datetime))'])
			->andWhere(['order.user_id' => $userIds])->with(['orderStatus'])->groupBy(['order.user_id', 'week'])->indexBy(function ($row) { return $row['user_id'] . '-' . $row['week']; });

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->innerJoinWith([
			'user',
			'enteredOrderStatus' => function ($query) {
				if ($this->fromDate) {
					$fromDate = gmdate('Y-m-d', strtotime('monday ' . $this->fromDate));
					$query->andWhere(['>=', 'create_datetime', $fromDate]);
				}
				if ($this->toDate) {
					$toDate = gmdate('Y-m-d', strtotime('monday next week ' . $this->toDate));
					$query->andWhere(['<', 'create_datetime', $toDate]);
				}
			},
			'orderProducts']);

		return $dataProvider;
	}
}
