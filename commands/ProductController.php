<?php

namespace app\commands;

use app\models\Product;
use app\models\User;
use Yii;
use yii\console\Controller;

class ProductController extends Controller {

    /**
     * Send an alert about products that are low on inventory.
     *
     * Send an email to all users that have the role "stock_manager", notifying
     * them about the products that went below the low stock threshold during
     * the course of the last week.
     */
    public function actionNotifyLowStock() {
        // Get products that went into a low-stock status duting the last week.
        $oneWeekAgoDate = date('Y-m-d', strtotime('-1 week'));
        $lowStockProducts = Product::find()->active()
            ->with(['model', 'model.brand'])->andWhere(['running_low' => true])
            ->andWhere(['>=', 'running_low_date', $oneWeekAgoDate])->all();
        // Get users that have the role "stock_manager".
        $userIds = Yii::$app->authManager->getUserIdsByRole('stock_manager');
        $users = User::find()->active()->andWhere(['id' => $userIds])->all();
        // Send email notification.
        $sendTo = array_column($users, 'email');
        Yii::$app->mailer
            ->compose('product/low-stock', ['products' => $lowStockProducts])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($sendTo)
            ->setSubject('Reporte semanal de stock bajo')
            ->send();
    }
}
