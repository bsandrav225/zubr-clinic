-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июл 28 2026 г., 21:38
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `zubr_clinic`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2a$12$5Is/XaylLLAor/oDFFgGxeVlEhCBJE4GJlrBnqWR4P/Z3e/CCxYTW', '2026-07-28 20:39:37');

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `doctor` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `time` varchar(10) NOT NULL,
  `complaint` text DEFAULT NULL,
  `status` enum('новая','подтверждена','завершена','отменена') DEFAULT 'новая',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`id`, `name`, `phone`, `email`, `service`, `doctor`, `date`, `time`, `complaint`, `status`, `created_at`, `updated_at`) VALUES
('b10001', 'Марина Козлова', '+7 (916) 234-56-78', NULL, 'Лечение зубов', 'Елена Морозова', '2026-07-29', '10:00', 'Ноющая боль в шестёрке справа сверху', 'новая', '2026-07-28 09:15:00', '2026-07-28 09:15:00'),
('b10002', 'Игорь Петров', '+7 (903) 111-22-33', NULL, 'Детский приём', 'Андрей Соколов', '2026-07-30', '11:30', 'Профилактический осмотр ребёнка 6 лет', 'подтверждена', '2026-07-27 14:40:00', '2026-07-28 10:00:00'),
('b10003', 'Анна Смирнова', '+7 (926) 555-77-88', NULL, 'Чистка зубов', 'Елена Морозова', '2026-07-25', '15:30', 'Хочу профессиональную гигиену перед отбеливанием', 'завершена', '2026-07-20 11:05:00', '2026-07-25 16:30:00'),
('b10004', 'Дмитрий Орлов', '+7 (495) 222-33-44', NULL, 'Удаление зубов', 'Андрей Соколов', '2026-08-01', '14:00', 'Зуб мудрости, периодически ноет', 'новая', '2026-07-28 18:20:00', '2026-07-28 18:20:00');

-- --------------------------------------------------------

--
-- Структура таблицы `doctors`
--

CREATE TABLE `doctors` (
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `focus` text DEFAULT NULL,
  `love` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `role`, `experience`, `focus`, `love`, `photo`, `sort_order`, `created_at`, `updated_at`) VALUES
('d1', 'Елена Морозова', 'Стоматолог-терапевт', '12 лет', 'лечение кариеса, эндодонтия, эстетические реставрации', 'повторное лечение каналов и аккуратные реставрации передних зубов', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=900&q=80', 1, '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
('d2', 'Андрей Соколов', 'Хирург, детский приём', '9 лет', 'удаление зубов, зубы мудрости, детский приём', 'находить подход к детям и спокойно проводить хирургический приём', 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=900&q=80', 2, '2026-07-28 20:39:37', '2026-07-28 20:39:37');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` varchar(20) NOT NULL,
  `author` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `date` date NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_visible` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `author`, `text`, `date`, `sort_order`, `is_visible`, `created_at`, `updated_at`) VALUES
('r1', 'Марина К.', 'Приняли в день обращения с острой болью. Всё объяснили заранее, лечение прошло спокойно.', '2026-05-12', 1, 1, '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
('r2', 'Игорь П.', 'Детский приём прошёл без слёз. Врач нашёл подход сразу — записались на профилактику.', '2026-04-03', 2, 1, '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
('r3', 'Анна С.', 'Цены на сайте совпали с итогом. Форма записи удобная — выбрала дату в календаре за минуту.', '2026-03-18', 3, 1, '2026-07-28 20:39:37', '2026-07-28 20:39:37');

-- --------------------------------------------------------

--
-- Структура таблицы `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL,
  `is_working` tinyint(1) DEFAULT 1,
  `hours_slots` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `schedule`
--

INSERT INTO `schedule` (`id`, `day_of_week`, `is_working`, `hours_slots`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '09:00,10:00,11:00,12:30,14:00,15:30,17:00,18:30', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(2, 2, 1, '09:00,10:00,11:00,12:30,14:00,15:30,17:00,18:30', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(3, 3, 1, '09:00,10:00,11:00,12:30,14:00,15:30,17:00,18:30', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(4, 4, 1, '09:00,10:00,11:00,12:30,14:00,15:30,17:00,18:30', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(5, 5, 1, '09:00,10:00,11:00,12:30,14:00,15:30,17:00,18:30', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(6, 6, 1, '10:00,11:30,13:00,15:00', '2026-07-28 20:39:37', '2026-07-28 20:39:37');

-- --------------------------------------------------------

--
-- Структура таблицы `schedule_exceptions`
--

CREATE TABLE `schedule_exceptions` (
  `id` int(11) NOT NULL,
  `exception_date` date NOT NULL,
  `is_off` tinyint(1) DEFAULT 0,
  `hours_slots` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE `services` (
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short` text DEFAULT NULL,
  `price` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `services`
--

INSERT INTO `services` (`id`, `name`, `short`, `price`, `duration`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES
('s1', 'Лечение зубов', 'Кариес, пульпит, каналы под микроскопом, реставрации.', 'от 3 500 ₽', '40–90 мин', 'Лечение под анестезией: сначала снимок, потом объём и цена.', 1, '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
('s2', 'Удаление зубов', 'Плановое и срочное удаление.', 'от 4 000 ₽', '30–60 мин', 'Плановое и срочное удаление с обезболиванием.', 2, '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
('s3', 'Чистка зубов', 'Ультразвук, Air Flow, полировка.', 'от 5 500 ₽', 'около 60 мин', 'Профессиональная гигиена для профилактики и свежести.', 3, '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
('s4', 'Отбеливание', 'Безопасное осветление эмали.', 'от 12 000 ₽', 'около 90 мин', 'Профессиональное отбеливание после осмотра.', 4, '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
('s5', 'Детский приём', 'Осмотр, лечение молочных зубов, герметизация фиссур.', 'от 2 500 ₽', '30–60 мин', 'Сначала знакомство с кабинетом, лечение — когда ребёнок готов.', 5, '2026-07-28 20:39:37', '2026-07-28 20:39:37');

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'clinic_name', 'Зубр', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(2, 'hero_title', 'Сначала снимок, потом лечение', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(3, 'hero_text', 'Кариес, удаление, чистка, импланты и детский приём. Дата и время выбираются в форме ниже.', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(4, 'phone', '+7 (495) 123-45-67', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(5, 'email', 'info@zubr-clinic.ru', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(6, 'address', 'г. Москва, ул. Примерная, 12', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(7, 'hours', 'Пн–Сб: 9:00–20:00\nВс: выходной', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(8, 'inn', '7700000000', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(9, 'telegram', 'https://t.me/', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(10, 'whatsapp', 'https://wa.me/', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(11, 'admin_email', 'admin@zubr-clinic.ru', '2026-07-28 20:39:37', '2026-07-28 20:39:37'),
(12, 'analytics', '', '2026-07-28 20:39:37', '2026-07-28 20:39:37');

-- --------------------------------------------------------

--
-- Структура таблицы `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_doctor` (`doctor`),
  ADD KEY `idx_service` (`service`);

--
-- Индексы таблицы `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visible_sort` (`is_visible`,`sort_order`);

--
-- Индексы таблицы `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_day_of_week` (`day_of_week`);

--
-- Индексы таблицы `schedule_exceptions`
--
ALTER TABLE `schedule_exceptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_exception_date` (`exception_date`);

--
-- Индексы таблицы `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Индексы таблицы `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `schedule_exceptions`
--
ALTER TABLE `schedule_exceptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
