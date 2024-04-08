<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/postgres/lib/get_tasks.inc';

$tasks = getUserTasks();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

echo json_encode($tasks);