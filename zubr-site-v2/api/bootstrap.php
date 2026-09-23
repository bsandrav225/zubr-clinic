<?php
declare(strict_types=1);

function zubr_is_local_host(): bool
{
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $host = (string)preg_replace('/:\d+$/', '', $host);
    return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
}

function zubr_public_base(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $appRoot = realpath(dirname(__DIR__));
    if ($docRoot && $appRoot) {
        $docRoot = str_replace('\\', '/', $docRoot);
        $appRoot = str_replace('\\', '/', $appRoot);
        if (str_starts_with($appRoot, $docRoot)) {
            $rel = trim(substr($appRoot, strlen($docRoot)), '/');
            $base = $rel === '' ? '/' : '/' . $rel . '/';
            return $base;
        }
    }

    $base = '/';
    return $base;
}

function zubr_detect_site_url(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
    return rtrim($scheme . '://' . $host . rtrim(zubr_public_base(), '/'), '/');
}

function app_config(): array {
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $configPath = __DIR__ . '/../config/config.php';
    $examplePath = __DIR__ . '/../config/config.example.php';

    if (!is_readable($configPath) && is_readable($examplePath)) {
        @copy($examplePath, $configPath);
    }

    if (!is_readable($configPath)) {
        http_response_code(500);
        die(
            'Файл конфигурации не найден. '
            . 'Скопируйте config/config.example.php в config/config.php и задайте параметры.'
        );
    }

    $loaded = require $configPath;
    if (!is_array($loaded)) {
        http_response_code(500);
        die('Некорректный формат config/config.php — ожидается массив.');
    }

    $config = $loaded;
    return $config;
}

function app_config_value(array $config, string $section, string $key, mixed $default = null): mixed {
    return $config[$section][$key] ?? $default;
}

$config = app_config();

$configuredUrl = rtrim((string)app_config_value($config, 'site', 'url', ''), '/');
$siteUrl = zubr_is_local_host() ? zubr_detect_site_url() : ($configuredUrl !== '' ? $configuredUrl : zubr_detect_site_url());

define('SITE_URL', $siteUrl);
define('SITE_NAME', (string)app_config_value($config, 'site', 'name', ''));
define('SITE_OG_IMAGE', (string)app_config_value($config, 'site', 'og_image', ''));
define('PUBLIC_BASE', zubr_public_base());
define('DB_HOST', (string)app_config_value($config, 'db', 'host', 'localhost'));
define('DB_NAME', (string)app_config_value($config, 'db', 'name', ''));
define('DB_USER', (string)app_config_value($config, 'db', 'user', ''));
define('DB_PASS', (string)app_config_value($config, 'db', 'pass', ''));
define('DB_CHARSET', (string)app_config_value($config, 'db', 'charset', 'utf8mb4'));
define('UPLOAD_DIR', (string)app_config_value($config, 'paths', 'upload_dir', __DIR__ . '/../uploads'));
define('APP_DEBUG', (bool)app_config_value($config, 'app', 'debug', false));

// Функция для подключения к БД
function get_db_connection(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            if (APP_DEBUG) {
                die('Ошибка подключения к базе данных: ' . $e->getMessage());
            }
            http_response_code(503);
            die('Сервис временно недоступен. Попробуйте позже.');
        }
    }
    
    return $pdo;
}

// Функции для работы с сессией
function start_admin_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Проверка администратора
function verify_admin(string $username, string $password): bool {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare('SELECT password_hash FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        
        if ($row && password_verify($password, $row['password_hash'])) {
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

// Функции для работы с данными (замена read_json/write_json)
function get_bookings(): array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->query('SELECT * FROM bookings ORDER BY created_at DESC');
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function get_booking_by_id(string $id): ?array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        return null;
    }
}

function update_booking_status(string $id, string $status): bool {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    } catch (PDOException $e) {
        return false;
    }
}

function get_services(): array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->query('SELECT * FROM services ORDER BY sort_order ASC, name ASC');
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function update_services(array $services): bool {
    try {
        $pdo = get_db_connection();
        $pdo->beginTransaction();
        
        foreach ($services as $id => $data) {
            $stmt = $pdo->prepare('
                UPDATE services 
                SET name = ?, short = ?, price = ?, duration = ?, description = ?
                WHERE id = ?
            ');
            $stmt->execute([
                $data['name'],
                $data['short'],
                $data['price'],
                $data['duration'],
                $data['description'],
                $id
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function get_doctors(): array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->query('SELECT * FROM doctors ORDER BY sort_order ASC, name ASC');
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function update_doctors(array $doctors): bool {
    try {
        $pdo = get_db_connection();
        $pdo->beginTransaction();
        
        foreach ($doctors as $id => $data) {
            $stmt = $pdo->prepare('
                UPDATE doctors 
                SET name = ?, role = ?, experience = ?, focus = ?, love = ?, photo = ?
                WHERE id = ?
            ');
            $stmt->execute([
                $data['name'],
                $data['role'],
                $data['experience'],
                $data['focus'],
                $data['love'],
                $data['photo'],
                $id
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function normalize_time_slots(array $slots): array {
    $out = [];
    foreach ($slots as $slot) {
        $slot = trim((string)$slot);
        if ($slot !== '' && preg_match('/^\d{1,2}:\d{2}$/', $slot)) {
            [$h, $m] = array_map('intval', explode(':', $slot));
            $out[] = sprintf('%02d:%02d', $h, $m);
        }
    }
    return array_values(array_unique($out));
}

function admin_time_options(array $selected = []): array {
    $options = [];
    for ($h = 8; $h <= 20; $h++) {
        foreach ([0, 30] as $m) {
            if ($h === 20 && $m > 0) {
                continue;
            }
            $options[] = sprintf('%02d:%02d', $h, $m);
        }
    }
    foreach (normalize_time_slots($selected) as $t) {
        if (!in_array($t, $options, true)) {
            $options[] = $t;
        }
    }
    sort($options);
    return $options;
}

function get_schedule(): array {
    try {
        $pdo = get_db_connection();
        
        // Получаем рабочие дни
        $stmt = $pdo->query('SELECT day_of_week, hours_slots FROM schedule WHERE is_working = 1 ORDER BY day_of_week');
        $weekdays = $stmt->fetchAll();
        
        // Получаем исключения
        $stmt = $pdo->query('SELECT exception_date, is_off, hours_slots FROM schedule_exceptions ORDER BY exception_date');
        $exceptions = $stmt->fetchAll();
        
        $result = [
            'weekdays' => [],
            'hours' => ['weekday' => [], 'saturday' => []],
            'exceptions' => []
        ];
        
        foreach ($weekdays as $day) {
            $result['weekdays'][] = (int)$day['day_of_week'];
            $slots = !empty($day['hours_slots'])
                ? normalize_time_slots(explode(',', (string)$day['hours_slots']))
                : [];
            if ((int)$day['day_of_week'] === 6) {
                $result['hours']['saturday'] = $slots;
            } else {
                $result['hours']['weekday'] = $slots;
            }
        }
        
        foreach ($exceptions as $ex) {
            $result['exceptions'][] = [
                'date' => $ex['exception_date'],
                'slots' => !empty($ex['is_off'])
                    ? []
                    : (!empty($ex['hours_slots'])
                        ? normalize_time_slots(explode(',', (string)$ex['hours_slots']))
                        : [])
            ];
        }
        
        return $result;
    } catch (PDOException $e) {
        return ['weekdays' => [1,2,3,4,5,6], 'hours' => ['weekday' => [], 'saturday' => []], 'exceptions' => []];
    }
}

function update_schedule(array $data): bool {
    try {
        $pdo = get_db_connection();
        $pdo->beginTransaction();
        
        // Обновляем расписание
        $pdo->exec('DELETE FROM schedule');
        
        $weekdays = $data['weekdays'] ?? [1,2,3,4,5,6];
        $weekdayHours = $data['hours']['weekday'] ?? [];
        $saturdayHours = $data['hours']['saturday'] ?? [];
        
        foreach ($weekdays as $day) {
            $hours = ($day == 6) ? implode(',', $saturdayHours) : implode(',', $weekdayHours);
            $stmt = $pdo->prepare('INSERT INTO schedule (day_of_week, is_working, hours_slots) VALUES (?, 1, ?)');
            $stmt->execute([$day, $hours]);
        }
        
        // Обновляем исключения
        $pdo->exec('DELETE FROM schedule_exceptions');
        
        foreach ($data['exceptions'] ?? [] as $ex) {
            $isOff = empty($ex['slots']);
            $hours = $isOff ? null : implode(',', $ex['slots']);
            $stmt = $pdo->prepare('INSERT INTO schedule_exceptions (exception_date, is_off, hours_slots) VALUES (?, ?, ?)');
            $stmt->execute([$ex['date'], $isOff, $hours]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function get_reviews(): array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->query('SELECT * FROM reviews ORDER BY sort_order ASC, created_at DESC');
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function update_reviews(array $reviews, ?array $newReview = null): bool {
    try {
        $pdo = get_db_connection();
        $pdo->beginTransaction();
        
        foreach ($reviews as $id => $data) {
            $stmt = $pdo->prepare('
                UPDATE reviews 
                SET author = ?, text = ?, date = ?, sort_order = ?, is_visible = ?
                WHERE id = ?
            ');
            $stmt->execute([
                $data['author'],
                $data['text'],
                $data['date'],
                (int)$data['order'],
                !empty($data['visible']) ? 1 : 0,
                $id
            ]);
        }
        
        if ($newReview && !empty($newReview['author']) && !empty($newReview['text'])) {
            $id = 'r' . bin2hex(random_bytes(3));
            $stmt = $pdo->prepare('
                INSERT INTO reviews (id, author, text, date, sort_order, is_visible) 
                VALUES (?, ?, ?, ?, ?, 1)
            ');
            $stmt->execute([
                $id,
                $newReview['author'],
                $newReview['text'],
                $newReview['date'],
                count($reviews) + 1
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function get_settings(): array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->query('SELECT setting_key, setting_value FROM settings');
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        return [];
    }
}

function update_settings(array $settings): bool {
    try {
        $pdo = get_db_connection();
        $pdo->beginTransaction();
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare('
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ');
            $stmt->execute([$key, $value, $value]);
        }
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function get_stats(): array {
    try {
        $pdo = get_db_connection();
        
        $stats = [
            'total' => 0,
            'new' => 0,
            'confirmed' => 0,
            'done' => 0,
        ];
        
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM bookings');
        $stats['total'] = (int)$stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'новая'");
        $stats['new'] = (int)$stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'подтверждена'");
        $stats['confirmed'] = (int)$stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'завершена'");
        $stats['done'] = (int)$stmt->fetch()['total'];
        
        return $stats;
    } catch (PDOException $e) {
        return ['total' => 0, 'new' => 0, 'confirmed' => 0, 'done' => 0];
    }
}

function get_bookings_by_service(): array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->query('
            SELECT 
                COALESCE(service, "Без услуги") as service_name,
                COUNT(*) as count
            FROM bookings
            GROUP BY service
            ORDER BY count DESC
        ');
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[$row['service_name']] = (int)$row['count'];
        }
        return $result;
    } catch (PDOException $e) {
        return [];
    }
}

function get_filtered_bookings(?string $status = null, ?string $doctor = null, ?string $service = null, ?string $date = null): array {
    try {
        $pdo = get_db_connection();
        $conditions = [];
        $params = [];
        
        if ($status) {
            $conditions[] = 'status = ?';
            $params[] = $status;
        }
        if ($doctor) {
            $conditions[] = 'doctor = ?';
            $params[] = $doctor;
        }
        if ($service) {
            $conditions[] = 'service = ?';
            $params[] = $service;
        }
        if ($date) {
            $conditions[] = 'date = ?';
            $params[] = $date;
        }
        
        $sql = 'SELECT * FROM bookings';
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY created_at DESC';
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function get_uploaded_files(): array {
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
        return [];
    }
    $files = scandir(UPLOAD_DIR);
    return array_values(array_filter($files, fn($f) => $f !== '.' && $f !== '..' && is_file(UPLOAD_DIR . '/' . $f)));
}

function hash_admin_password(string $password): string {
    return password_hash($password, PASSWORD_DEFAULT);
}

function json_response(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return is_string($ip) ? $ip : '0.0.0.0';
}

function rate_limit(string $key, int $max, int $windowSeconds): bool {
    $dir = sys_get_temp_dir() . '/zubr_rate';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $file = $dir . '/' . preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $key) . '.json';
    $now = time();
    $hits = [];
    if (is_file($file)) {
        $raw = json_decode((string)file_get_contents($file), true);
        if (is_array($raw)) {
            $hits = array_values(array_filter($raw, fn($t) => is_int($t) && $t > $now - $windowSeconds));
        }
    }
    if (count($hits) >= $max) {
        return false;
    }
    $hits[] = $now;
    file_put_contents($file, json_encode($hits), LOCK_EX);
    return true;
}

function booked_times_for_date(string $date): array {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("
            SELECT time FROM bookings
            WHERE date = ? AND status IN ('новая', 'подтверждена')
        ");
        $stmt->execute([$date]);
        return array_column($stmt->fetchAll(), 'time');
    } catch (PDOException $e) {
        return [];
    }
}

function slots_for_date(string $date): array {
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt || $dt->format('Y-m-d') !== $date) {
        return [];
    }

    $schedule = get_schedule();
    $slots = [];

    foreach ($schedule['exceptions'] ?? [] as $ex) {
        if (($ex['date'] ?? '') === $date) {
            $slots = normalize_time_slots($ex['slots'] ?? []);
            $booked = booked_times_for_date($date);
            return array_values(array_diff($slots, $booked));
        }
    }

    $dow = (int)$dt->format('N');
    $weekdays = array_map('intval', $schedule['weekdays'] ?? []);
    if (!in_array($dow, $weekdays, true)) {
        return [];
    }

    $hours = $schedule['hours'] ?? [];
    $slots = normalize_time_slots(
        $dow === 6 ? ($hours['saturday'] ?? []) : ($hours['weekday'] ?? [])
    );
    $booked = booked_times_for_date($date);
    return array_values(array_diff($slots, $booked));
}

function create_booking(array $booking): bool {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare('
            INSERT INTO bookings (id, name, phone, email, service, doctor, date, time, complaint, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $booking['id'],
            $booking['name'],
            $booking['phone'],
            $booking['email'] ?? null,
            $booking['service'] ?: null,
            $booking['doctor'] ?: null,
            $booking['date'],
            $booking['time'],
            $booking['complaint'],
            $booking['status'] ?? 'новая',
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

function send_admin_mail(array $booking): void {
    $settings = get_settings();
    $to = trim((string)($settings['admin_email'] ?? ''));
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return;
    }
    $subject = 'Новая заявка — ' . ($settings['clinic_name'] ?? 'Зубр');
    $lines = [
        'Имя: ' . ($booking['name'] ?? ''),
        'Телефон: ' . ($booking['phone'] ?? ''),
        'Дата: ' . ($booking['date'] ?? '') . ' ' . ($booking['time'] ?? ''),
        'Услуга: ' . ($booking['service'] ?? '—'),
        'Врач: ' . ($booking['doctor'] ?? '—'),
        'Жалоба: ' . ($booking['complaint'] ?? ''),
    ];
    $body = implode("\n", $lines);
    $headers = 'Content-Type: text/plain; charset=UTF-8' . "\r\n" .
        'From: noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
}

function ensure_upload_dir(): bool {
    if (!is_dir(UPLOAD_DIR)) {
        return mkdir(UPLOAD_DIR, 0755, true);
    }
    return true;
}

function save_uploaded_image(array $file, string $prefix = 'img'): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }
    if (($file['size'] ?? 0) <= 0 || ($file['size'] ?? 0) > 5_000_000) {
        return null;
    }

    $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: '';
    $allowedMime = [
        'image/jpeg' => true,
        'image/png' => true,
        'image/webp' => true,
        'image/gif' => true,
    ];
    if (!isset($allowedMime[$mime])) {
        return null;
    }

    if (!ensure_upload_dir()) {
        return null;
    }

    $name = $prefix . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
    $dest = UPLOAD_DIR . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return 'uploads/' . $name;
}