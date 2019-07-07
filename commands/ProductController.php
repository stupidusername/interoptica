<?php

namespace app\commands;

use yii\console\Controller;

class ProductController extends Controller {

    /**
     * Send an alert about products that are low on inventory.
     *
     * Send an email to all users that have the role "stock manager", notifying
     * them about the products that went below the low stock threshold during
     * the course of the last week.
     */
    public function actionNotifyLowStock() {

    }
}
