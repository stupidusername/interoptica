<?php

namespace app\components;

use yii\db\ActiveQuery;

class DeletedQuery extends ActiveQuery {
	
	public function active() {
		$modelClass = $this->modelClass;
		$field = $modelClass::tableName() . '.deleted';
		return $this->andWhere(['or', [$field => null], [$field => 0]]);
	}
}