<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$ip = client_ip();
if (!rate_limit('book_' . $ip, 5, 600)) {
    json_response(['ok' => false, 'error' => 'Слишком много заявок. Попробуйте позже.'], 429);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
if (!is_array($input)) {
    $input = $_POST;
}

// Honeypot
if (!empty($input['website'])) {
    json_response(['ok' => true, 'message' => 'Заявка принята.']);
}

$name = trim((string)($input['name'] ?? ''));
$phone = trim((string)($input['phone'] ?? ''));
$complaint = trim((string)($input['complaint'] ?? ''));
$date = trim((string)($input['date'] ?? ''));
$time = trim((string)($input['time'] ?? ''));
$service = trim((string)($input['service'] ?? ''));
$doctor = trim((string)($input['doctor'] ?? ''));
$comment = trim((string)($input['comment'] ?? ''));
$consent = !empty($input['consent']);

$digits = preg_replace('/\D+/', '', $phone) ?? '';
$errors = [];
if ($name === '') $errors[] = 'Укажите имя';
if (strlen($digits) < 10) $errors[] = 'Укажите корректный телефон';
if ($complaint === '') $errors[] = 'Опишите, что болит';
if ($date === '' || $time === '') $errors[] = 'Выберите дату и время';
if (!$consent) $errors[] = 'Нужно согласие на обработку данных';

$available = slots_for_date($date);
if ($date && $time && !in_array($time, $available, true)) {
    $errors[] = 'Выбранный слот недоступен';
}

if ($errors) {
    json_response(['ok' => false, 'error' => implode('. ', $errors)], 422);
}

if ($comment !== '') {
    $complaint .= "\n\nКомментарий: " . $comment;
}

$booking = [
    'id' => 'b' . bin2hex(random_bytes(6)),
    'name' => $name,
    'phone' => $phone,
    'complaint' => $complaint,
    'date' => $date,
    'time' => $time,
    'service' => $service,
    'doctor' => $doctor,
    'status' => 'новая',
];

if (!create_booking($booking)) {
    json_response(['ok' => false, 'error' => 'Не удалось сохранить заявку'], 500);
}

send_admin_mail($booking);

json_response([
    'ok' => true,
    'message' => 'Заявка отправлена. Мы свяжемся с вами для подтверждения.',
    'id' => $booking['id'],
]);
