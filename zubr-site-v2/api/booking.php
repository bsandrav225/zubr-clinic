<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Метод не разрешён'], 405);
}

session_start();
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    json_response(['success' => false, 'error' => 'Недействительный токен'], 403);
}

function sanitize_input(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$name = sanitize_input($_POST['name'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : null;
$service = isset($_POST['service']) && !empty($_POST['service']) ? sanitize_input($_POST['service']) : null;
$doctor = isset($_POST['doctor']) && !empty($_POST['doctor']) ? sanitize_input($_POST['doctor']) : null;
$date = sanitize_input($_POST['date'] ?? '');
$time = sanitize_input($_POST['time'] ?? '');
$complaint = sanitize_input($_POST['complaint'] ?? '');
$comment = isset($_POST['comment']) ? sanitize_input($_POST['comment']) : null;
$consent = isset($_POST['consent']);

$errors = [];

if (empty($name)) {
    $errors['name'] = 'Имя обязательно для заполнения';
} elseif (strlen($name) < 2) {
    $errors['name'] = 'Имя должно содержать минимум 2 символа';
} elseif (strlen($name) > 100) {
    $errors['name'] = 'Имя не должно превышать 100 символов';
}

if (empty($phone)) {
    $errors['phone'] = 'Телефон обязателен для заполнения';
} elseif (!preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $phone)) {
    $errors['phone'] = 'Введите корректный номер телефона';
}

if (empty($date)) {
    $errors['date'] = 'Дата обязательна для заполнения';
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $errors['date'] = 'Неверный формат даты';
} else {
    $today = date('Y-m-d');
    if ($date < $today) {
        $errors['date'] = 'Дата не может быть в прошлом';
    }
}

if (empty($time)) {
    $errors['time'] = 'Время обязательно для заполнения';
} elseif (!preg_match('/^\d{2}:\d{2}$/', $time)) {
    $errors['time'] = 'Неверный формат времени';
}

if (empty($complaint)) {
    $errors['complaint'] = 'Опишите жалобу или причину визита';
} elseif (strlen($complaint) < 3) {
    $errors['complaint'] = 'Опишите жалобу более подробно (минимум 3 символа)';
} elseif (strlen($complaint) > 1000) {
    $errors['complaint'] = 'Текст не должен превышать 1000 символов';
}

if (!$consent) {
    $errors['consent'] = 'Необходимо согласие на обработку персональных данных';
}

if (!empty($errors)) {
    json_response([
        'success' => false,
        'errors' => $errors,
        'message' => 'Пожалуйста, исправьте ошибки в форме'
    ], 400);
}

$website = $_POST['website'] ?? '';
if (!empty($website)) {
    json_response(['success' => false, 'error' => 'Подозрительная активность'], 403);
}

$ip = client_ip();
if (!rate_limit('booking_' . $ip, 5, 3600)) {
    json_response([
        'success' => false,
        'error' => 'Слишком много заявок. Пожалуйста, подождите час перед повторной отправкой'
    ], 429);
}

$availableSlots = slots_for_date($date);
if (!in_array($time, $availableSlots, true)) {
    json_response([
        'success' => false,
        'error' => 'Выбранное время уже занято. Пожалуйста, выберите другое время'
    ], 409);
}

$bookingId = 'b' . date('ymd') . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

$bookingData = [
    'id' => $bookingId,
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'service' => $service,
    'doctor' => $doctor,
    'date' => $date,
    'time' => $time,
    'complaint' => $complaint,
    'status' => 'новая',
];

try {
    if (create_booking($bookingData)) {
        send_admin_mail($bookingData);

        session_start();
        $_SESSION['booking_success'] = [
            'id' => $bookingId,
            'date' => $date,
            'time' => $time,
            'doctor' => $doctor,
            'service' => $service
        ];

        unset($_SESSION['csrf_token']);
        
        json_response([
            'success' => true,
            'message' => 'Заявка успешно отправлена!',
            'booking_id' => $bookingId,
            'data' => [
                'date' => $date,
                'time' => $time,
                'doctor' => $doctor,
                'service' => $service
            ]
        ]);
    } else {
        throw new Exception('Не удалось сохранить заявку в базу данных');
    }
} catch (Exception $e) {
    error_log('Booking error: ' . $e->getMessage());
    json_response([
        'success' => false,
        'error' => 'Произошла ошибка при отправке заявки. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.'
    ], 500);
}