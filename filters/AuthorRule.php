<?php

namespace app\filters;

use Yii;

class AuthorRule extends \yii\filters\AccessRule
{
	/**
	 * Name of the model attribute that holds the user id
	 * @var string
	 */
	public $authorIdAttribute;
	
	/**
	 * An anonymous function without params that retrieves the model that should
	 * be used for validation.
	 * @var Closure
	 */
	public $model;
	
	/**
	 * Optional. If set the controller field of this name will be populated with
	 * the model returned by $model. This could be used to avoid extra queries.
	 * @var string
	 */
	public $modelField = null;
	
    /**
     * @inheritdoc
     * */
    protected function matchRole($user)
    {
		$model = null;
		if ($this->modelField && $this->model) {
			$model = call_user_func($this->model);
			Yii::$app->controller->{$this->modelField} = $model;
		}
		
        if (empty($this->roles)) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role === '?') {
                if (\Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!\Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === 'admin') {
				if (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isAdmin) {
                    return true;
                }
			} elseif ($role === 'author') {
				if (!$model && $this->model) {
					$model = call_user_func($this->model);
				}
				if ($model && !\Yii::$app->user->isGuest && $model->{$this->authorIdAttribute} == Yii::$app->user->id) {
					return true;
				}
            } elseif ($user->can($role)) {
                return true;
            }
        }

        return false;
    }
}