<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Delivery;

/**
 * DeliverySearch represents the model behind the search form about `app\models\Delivery`.
 */
class DeliverySearch extends Delivery
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'status', 'user_id'], 'integer'],
			[['transport', 'tracking_number'], 'safe'],
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
		$query = Delivery::find()->with([
			'issues.customer',
			'orders.customer',
			'deliveryStatus',
			'user',
			'enteredDeliveryStatus',
			'issues.issueStatus',
			'orders.orderStatus',
		]);

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
			'user_id' => $this->user_id,
		]);

		$query->andFilterWhere(['like', 'transport', $this->transport]);
		$query->andFilterWhere(['like', 'tracking_number', $this->tracking_number]);

		if ($this->status != null) {
			$query->innerJoinWith(['deliveryStatuses' => function ($query) {
				$subQuery = DeliveryStatus::getLastStatuses();
				$query->andWhere([DeliveryStatus::tableName() . '.id' => $subQuery, 'status' => $this->status]);
			}]);
		}

		return $dataProvider;
	}
}
