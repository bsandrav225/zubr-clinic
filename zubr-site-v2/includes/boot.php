<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/api/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$BASE = PUBLIC_BASE === '/' ? '/' : PUBLIC_BASE;
$clinic = require __DIR__ . '/clinic-data.php';
$settings = get_settings();

$phone = $settings['phone'] ?? '+7 (495) 123-45-67';
$email = $settings['email'] ?? 'info@zubr-clinic.ru';
$address = $settings['address'] ?? 'г. Москва, ул. Примерная, 12';
$hours = $settings['hours'] ?? "Пн–Сб: 9:00–20:00\nВс: выходной";
$inn = $settings['inn'] ?? '7700000000';
$telegram = $settings['telegram'] ?? 'https://t.me/';
$whatsapp = $settings['whatsapp'] ?? 'https://wa.me/';
$hero_title = $settings['hero_title'] ?? 'Стоматология, которая начинается с точной диагностики';
$hero_text = $settings['hero_text'] ?? $clinic['about_lead'];
$clinic_name = $settings['clinic_name'] ?? 'Зубр';
$csrf = $_SESSION['csrf_token'];
$phone_href = (string)preg_replace('/[^\d+]/', '', (string)$phone);

$doctors = get_doctors();
if (!$doctors) {
    $raw = @file_get_contents(dirname(__DIR__) . '/data/doctors.json');
    $doctors = json_decode((string)$raw, true) ?: [];
}

$reviews = get_reviews();
if (!$reviews) {
    $raw = @file_get_contents(dirname(__DIR__) . '/data/reviews.json');
    $reviews = json_decode((string)$raw, true) ?: [];
}

$dbServices = get_services();
$catalog = $clinic['services'];

function zubr_hours_html(string $hours): string
{
    $hours = str_ireplace(['<br />', '<br/>', '<br>'], "\n", $hours);
    return nl2br(htmlspecialchars($hours, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false);
}

function zubr_service_url(string $slug, string $base = ''): string
{
    global $clinic;
    $name = $clinic['services'][$slug]['name'] ?? $slug;
    return $base . 'services/service_template.php?service=' . rawurlencode($name);
}

function zubr_find_service(string $query): ?array
{
    global $clinic;
    $query = trim($query);
    if ($query === '') {
        return null;
    }
    foreach ($clinic['services'] as $slug => $service) {
        if ($slug === $query || ($service['name'] ?? '') === $query) {
            return $service + ['slug' => $slug];
        }
    }
    return null;
}

function zubr_booking_services(): array
{
    global $dbServices, $clinic;
    $byName = [];
    foreach ($dbServices as $service) {
        if (!empty($service['name'])) {
            $byName[$service['name']] = $service;
        }
    }
    foreach ($clinic['services'] as $slug => $service) {
        $name = $service['name'] ?? '';
        if ($name !== '' && !isset($byName[$name])) {
            $byName[$name] = $service + ['slug' => $slug];
        }
    }
    return array_values($byName);
}

function zubr_logo_svg(int $size = 34): string
{
    return '<svg viewBox="0 0 40 40" width="' . $size . '" height="' . $size . '" fill="none" aria-hidden="true">'
        . '<rect width="40" height="40" rx="12" fill="currentColor"/>'
        . '<path d="M11 25.5c2.2-5.2 5.4-8.8 9-8.8s6.8 3.6 9 8.8" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>'
        . '<path d="M14.5 18c1-2 2.6-3.4 5.5-3.4s4.5 1.4 5.5 3.4" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>'
        . '<circle cx="17" cy="15.2" r="1.5" fill="#fff"/>'
        . '<circle cx="23" cy="15.2" r="1.5" fill="#fff"/>'
        . '</svg>';
}
