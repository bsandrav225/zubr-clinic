<?php
/**
 * Пример конфигурации сайта.
 *
 * При первом запуске файл копируется в config.php автоматически.
 * Можно скопировать вручную:
 *   copy config.example.php config.php
 *
 * Значения ниже подходят для XAMPP по умолчанию
 * (MySQL: пользователь root, пароль пустой).
 *
 * Файл config.php не должен попадать в git (см. .gitignore).
 */
declare(strict_types=1);

return [
    'site' => [
        // На localhost адрес подставится сам. На хостинге укажите боевой домен.
        'url' => 'https://zubr-clinic.ru',
        'name' => 'Клиника «Зубр»',
        'og_image' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1200&q=80',
    ],

    'db' => [
        'host' => 'localhost',
        'name' => 'zubr_clinic',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],

    'paths' => [
        'upload_dir' => __DIR__ . '/../uploads',
    ],

    'app' => [
        // true — показывать детали ошибок БД (только для локальной разработки)
        'debug' => false,
    ],
];
