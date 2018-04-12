<?php

return [
  'class' => \yii\queue\redis\Queue::class,
  'as log' => \yii\queue\LogBehavior::class,
  'redis' => 'redis',
  'channel' => 'queue',
  'ttr' => 60, // Max time for job execution
  'attempts' => 3, // Max number of attempts
];
