<?php

namespace app\filters;

use yii\base\ActionFilter;
use app\models\OrderStatus;
use yii\web\ForbiddenHttpException;
use Yii;

/**
 * This rule only allows to edit entries of orders that are
 * in a OrderStatus::STATUS_LOADING state
 */
class OrderStatusRule extends ActionFilter
{
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param Action $action the action to be executed.
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->controller->model) {
            $orderId = Yii::$app->request->getQueryParam('orderId');
            if ($orderId) {
                Yii::$app->controller->model = Yii::$app->controller->findModel($orderId);
            }
        }
        if (Yii::$app->controller->model->status != OrderStatus::STATUS_LOADING) {
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
        throw new ForbiddenHttpException('No se puede editar un pedido que no esta en estado ' . OrderStatus::statusLabels()[OrderStatus::STATUS_LOADING] . '.');
    }
}
