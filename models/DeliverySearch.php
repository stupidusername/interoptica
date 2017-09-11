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
	 * @var integer
	 */
	public $customerId;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'customerId', 'status', 'user_id'], 'integer'],
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
			'deliveryStatus',
			'user',
			'enteredDeliveryStatus',
			'issues.issueStatus',
			'orders.orderStatus',
		]);

		$query->joinWith([
			'issues.customer issue_customer' => function ($query) { $query->where([]); },
			'orders.customer order_customer' => function ($query) { $query->where([]); },
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
			'delivery.id' => $this->id,
			'delivery.user_id' => $this->user_id,
		]);

		$query->andFilterWhere(['like', 'transport', $this->transport]);
		$query->andFilterWhere(['like', 'tracking_number', $this->tracking_number]);
		$query->andFilterWhere(['or', ['issue_customer.id' => $this->customerId], ['order_customer.id' => $this->customerId]]);

		if ($this->status != null) {
			$query->innerJoinWith(['deliveryStatuses' => function ($query) {
				$subQuery = DeliveryStatus::getLastStatuses();
				$query->andWhere([DeliveryStatus::tableName() . '.id' => $subQuery, 'status' => $this->status]);
			}]);
		}

		return $dataProvider;
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustomer() {
		return $this->hasOne(Customer::className(), ['id' => 'customerId']);
	}
}
