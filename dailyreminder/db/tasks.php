<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'local_dailyreminder\task\send_daily_reminder',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ],
];