<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;

/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Order
{
	/**
	 * @var integer
	 */
	public $status;

	/**
	 * @var string
	 */
	public $fromDate;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'user_id', 'customer_id', 'status'], 'integer'],
			[['comment', 'fromDate'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
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
		$query = Order::find()->with(['user', 'customer', 'orderStatus', 'orderProducts.product.model']);

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

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			// disambiguate if needed
			$this->status != null ? 'order.user_id' : 'user_id' => $this->user_id,
			'customer_id' => $this->customer_id,
		]);

		$query->joinWith([
			'enteredOrderStatus' => function ($query) {
				if ($this->fromDate) {
					$query->andWhere(['>=', 'create_datetime', gmdate('Y-m-d', strtotime($this->fromDate))]);
					$query->andWhere(['<', 'create_datetime', gmdate('Y-m-d', strtotime($this->fromDate . ' +1 day'))]);
				}
			},
		]);

		if ($this->status != null) {
			$query->innerJoinWith(['orderStatuses' => function ($query) {
				$subQuery = OrderStatus::getLastStatuses();
				$query->andWhere([OrderStatus::tableName() . '.id' => $subQuery, 'status' => $this->status]);
			}]);
		}

		$query->andFilterWhere(['like', 'comment', $this->comment]);

		return $dataProvider;
	}
}
