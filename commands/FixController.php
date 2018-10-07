<?php

namespace app\commands;

use app\models\Delivery;
use app\models\IssueStatus;
use app\models\OrderStatus;
use yii\console\Controller;
use yii\console\ExitCode;

class FixController extends Controller {

  public function actionDeliveries() {
    $deliveries = Delivery::find()->innerJoinWith([
      'orders.orderStatus',
      'deliveryStatus',
    ])->where(['<', 'order_status.status', OrderStatus::STATUS_WAITING_FOR_TRANSPORT])->all();

    foreach ($deliveries as $delivery) {
      foreach ($delivery->orders as $order) {
        if ($order->orderStatus->status != Delivery::orderStatusMap()[$delivery->deliveryStatus->status]) {
          $status = new OrderStatus();
          $status->order_id = $order->id;
          $status->status = Delivery::orderStatusMap()[$delivery->deliveryStatus->status];
          $status->create_datetime = $delivery->deliveryStatus->create_datetime;
          $status->user_id = $delivery->deliveryStatus->user_id;
          $status->save();
        }
      }
    }

    $deliveries = Delivery::find()->innerJoinWith([
      'issues.issueStatus',
      'deliveryStatus',
    ])->where(['<', 'issue_status.status', IssueStatus::STATUS_WAITING_FOR_TRANSPORT])->all();

    foreach ($deliveries as $delivery) {
      foreach ($delivery->issues as $issue) {
        if ($issue->issueStatus->status != Delivery::issueStatusMap()[$delivery->deliveryStatus->status]) {
          $status = new IssueStatus();
          $status->issue_id = $issue->id;
          $status->status = Delivery::issueStatusMap()[$delivery->deliveryStatus->status];
          $status->create_datetime = $delivery->deliveryStatus->create_datetime;
          $status->user_id = $delivery->deliveryStatus->user_id;
          $status->save();
        }
      }
    }

    return ExitCode::OK;
  }
}
