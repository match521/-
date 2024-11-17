<?php
header('Content-Type: application/json');

$logs = file('logs.txt', FILE_IGNORE_NEW_LINES);

echo json_encode(['logs' => $logs]);
