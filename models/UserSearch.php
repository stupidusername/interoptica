<?php

namespace app\models;

use dektrium\user\models\UserSearch as BaseUserSearch;

class UserSearch extends BaseUserSearch {

	/**
	 * @inheritdoc
	 */
	public function search($params)
	{
			$dataProvider = parent::search($params);
			$dataProvider->query->active();
			return $dataProvider;
	}
}
