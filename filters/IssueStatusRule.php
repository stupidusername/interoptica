<?php

namespace app\filters;

use yii\base\ActionFilter;
use app\models\IssueStatus;
use yii\web\ForbiddenHttpException;
use Yii;

/**
 * This rule only allows to edit entries of issues that are
 * in a IssueStatus::STATUS_OPEN state
 */
class IssueStatusRule extends ActionFilter
{
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param Action $action the action to be executed.
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (Yii::$app->controller->model->status != IssueStatus::STATUS_OPEN) {
			$this->denyAccess();
		}
		return true;
    }

    /**
     * Denies the access of the user.
     * @throws ForbiddenHttpException.
     */
    protected function denyAccess()
    {
        throw new ForbiddenHttpException('No se puede editar un reclamo que no esta en estado ' . IssueStatus::statusLabels()[IssueStatus::STATUS_OPEN] . '.');
    }
}
