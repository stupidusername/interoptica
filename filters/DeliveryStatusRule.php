<?php

namespace app\filters;

use yii\base\ActionFilter;
use app\models\DeliveryStatus;
use yii\web\ForbiddenHttpException;
use Yii;

/**
 * This rule only allows to edit entries of deliveries that are
 * in a DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT state
 */
class DeliveryStatusRule extends ActionFilter
{
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param Action $action the action to be executed.
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (!in_array(Yii::$app->controller->model->status, [DeliveryStatus::STATUS_ERROR, DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT])) {
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
        throw new ForbiddenHttpException('No se puede editar un envio que no esta en estado ' . DeliveryStatus::statusLabels()[DeliveryStatus::STATUS_WAITING_FOR_TRANSPORT] . '.');
    }
}
