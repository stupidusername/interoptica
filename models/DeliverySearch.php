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
	 * @var string
	 */
	public $invoiceNumbers;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'customerId', 'status', 'user_id'], 'integer'],
			[['tracking_number', 'invoiceNumbers', 'transport_id'], 'safe'],
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
			'issues.issueInvoices',
			'orders.orderInvoices',
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
			'delivery.transport_id' => $this->transport_id,
		]);

		$query->andFilterWhere(['like', 'tracking_number', $this->tracking_number]);
		$query->andFilterWhere(['or', ['issue_customer.id' => $this->customerId], ['order_customer.id' => $this->customerId]]);

		if ($this->status != null) {
			$query->innerJoinWith(['deliveryStatuses' => function ($query) {
				$subQuery = DeliveryStatus::getLastStatuses();
				$query->andWhere([DeliveryStatus::tableName() . '.id' => $subQuery, 'status' => $this->status]);
			}]);
		}

		if ($this->invoiceNumbers) {
			$query1 = DeliveryIssue::find()->select('delivery_id')->innerJoinWith([
				'issue.issueInvoices' => function ($query) {
					$query->andWhere(['like', 'number', $this->invoiceNumbers]);
				},
			]);
			$query2 = DeliveryOrder::find()->select('delivery_id')->innerJoinWith([
				'order.orderInvoices' => function ($query) {
					$query->andWhere(['like', 'number', $this->invoiceNumbers]);
				},
			]);
			$query->andWhere(['or', ['delivery.id' => $query1], ['delivery.id' => $query2]]);
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
