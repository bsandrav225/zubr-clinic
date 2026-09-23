<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, must-revalidate');

$date = $_GET['date'] ?? '';

if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    json_response(['success' => false, 'error' => 'Неверный формат даты'], 400);
}

$slots = slots_for_date($date);

json_response([
    'success' => true,
    'date' => $date,
    'slots' => $slots,
    'is_working' => !empty($slots)
]);