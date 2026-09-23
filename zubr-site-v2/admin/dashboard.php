<?php
declare(strict_types=1);
require __DIR__ . '/../api/bootstrap.php';
start_admin_session();

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['admin_ok'])) {
    header('Location: index.php');
    exit;
}

$flash = '';
$section = $_GET['section'] ?? 'bookings';
$adminName = (string)($_SESSION['admin_user'] ?? 'Админ');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_booking_status') {
        $id = $_POST['id'] ?? '';
        $status = $_POST['status'] ?? 'новая';
        $allowed = ['новая', 'подтверждена', 'завершена', 'отменена'];
        if (in_array($status, $allowed, true) && update_booking_status($id, $status)) {
            $flash = 'Статус заявки обновлён';
        }
        $section = 'bookings';
    }

    if ($action === 'save_services') {
        $services = get_services();
        $updateData = [];
        foreach ($services as $s) {
            $id = $s['id'];
            if (isset($_POST['name'][$id])) {
                $updateData[$id] = [
                    'name' => trim((string)$_POST['name'][$id]),
                    'short' => trim((string)$_POST['short'][$id]),
                    'price' => trim((string)$_POST['price'][$id]),
                    'duration' => trim((string)$_POST['duration'][$id]),
                    'description' => trim((string)$_POST['description'][$id]),
                ];
            }
        }
        if (update_services($updateData)) {
            $flash = 'Услуги сохранены';
        }
        $section = 'services';
    }

    if ($action === 'save_doctors') {
        $doctors = get_doctors();
        $updateData = [];
        foreach ($doctors as $d) {
            $id = $d['id'];
            if (!isset($_POST['name'][$id])) {
                continue;
            }

            $photo = trim((string)($_POST['photo'][$id] ?? $d['photo'] ?? ''));
            if (!empty($_FILES['photo_file']['name'][$id])) {
                $file = [
                    'name' => $_FILES['photo_file']['name'][$id] ?? '',
                    'type' => $_FILES['photo_file']['type'][$id] ?? '',
                    'tmp_name' => $_FILES['photo_file']['tmp_name'][$id] ?? '',
                    'error' => $_FILES['photo_file']['error'][$id] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $_FILES['photo_file']['size'][$id] ?? 0,
                ];
                $uploaded = save_uploaded_image($file, 'doctor');
                if ($uploaded) {
                    $photo = $uploaded;
                } else {
                    $flash = 'Не удалось загрузить фото для одного из врачей (jpg/png/webp до 5 МБ)';
                }
            }

            $updateData[$id] = [
                'name' => trim((string)$_POST['name'][$id]),
                'role' => trim((string)$_POST['role'][$id]),
                'experience' => trim((string)$_POST['experience'][$id]),
                'focus' => trim((string)$_POST['focus'][$id]),
                'love' => trim((string)$_POST['love'][$id]),
                'photo' => $photo,
            ];
        }
        if ($updateData && update_doctors($updateData)) {
            $flash = $flash !== '' ? $flash : 'Карточки врачей сохранены';
        }
        $section = 'doctors';
    }

    if ($action === 'save_schedule') {
        $weekday = normalize_time_slots($_POST['weekday_hours'] ?? []);
        $saturday = normalize_time_slots($_POST['saturday_hours'] ?? []);
        $days = [];
        foreach ([1, 2, 3, 4, 5, 6, 7] as $d) {
            if (!empty($_POST['day_' . $d])) {
                $days[] = $d;
            }
        }
        $exceptions = [];
        $exDates = $_POST['ex_date'] ?? [];
        $exOff = $_POST['ex_off'] ?? [];
        $exSlots = $_POST['ex_slots'] ?? [];
        if (is_array($exDates)) {
            foreach ($exDates as $i => $dateRaw) {
                $datePart = trim((string)$dateRaw);
                if ($datePart === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $datePart)) {
                    continue;
                }
                $isOff = !empty($exOff[$i]);
                if ($isOff) {
                    $exceptions[] = ['date' => $datePart, 'slots' => []];
                } else {
                    $slotList = is_array($exSlots[$i] ?? null) ? $exSlots[$i] : [];
                    $exceptions[] = [
                        'date' => $datePart,
                        'slots' => normalize_time_slots($slotList),
                    ];
                }
            }
        }

        $scheduleData = [
            'weekdays' => $days ?: [1, 2, 3, 4, 5, 6],
            'hours' => ['weekday' => $weekday, 'saturday' => $saturday],
            'exceptions' => $exceptions,
        ];

        if (update_schedule($scheduleData)) {
            $flash = 'Расписание сохранено';
        }
        $section = 'schedule';
    }

    if ($action === 'save_reviews') {
        $reviews = get_reviews();
        $updateData = [];
        foreach ($reviews as $r) {
            $id = $r['id'];
            if (isset($_POST['author'][$id])) {
                $updateData[$id] = [
                    'author' => trim((string)$_POST['author'][$id]),
                    'text' => trim((string)$_POST['text'][$id]),
                    'date' => trim((string)$_POST['date'][$id]),
                    'order' => (int)($_POST['order'][$id] ?? 0),
                    'visible' => !empty($_POST['visible'][$id]),
                ];
            }
        }

        $newReview = null;
        if (!empty($_POST['new_author']) && !empty($_POST['new_text'])) {
            $newReview = [
                'author' => trim((string)$_POST['new_author']),
                'text' => trim((string)$_POST['new_text']),
                'date' => trim((string)($_POST['new_date'] ?: date('Y-m-d'))),
            ];
        }

        if (update_reviews($updateData, $newReview)) {
            $flash = 'Отзывы сохранены';
        }
        $section = 'reviews';
    }

    if ($action === 'save_settings') {
        $settings = [];
        foreach (['clinic_name','phone','email','address','hours','inn','telegram','whatsapp','hero_title','hero_text','admin_email','analytics'] as $key) {
            if (isset($_POST[$key])) {
                $settings[$key] = trim((string)$_POST[$key]);
            }
        }
        if (update_settings($settings)) {
            $flash = 'Настройки сохранены';
        }
        $section = 'settings';
    }

    if ($action === 'upload_image') {
        if (!empty($_FILES['image']['tmp_name'])) {
            $path = save_uploaded_image($_FILES['image'], 'img');
            $flash = $path ? ('Файл загружен: ' . $path) : 'Недопустимый файл (jpg/png/webp до 5 МБ)';
        } else {
            $flash = 'Выберите файл для загрузки';
        }
        $section = 'uploads';
    }
}

$bookings = get_bookings();
$services = get_services();
$doctors = get_doctors();
$schedule = get_schedule();
$reviews = get_reviews();
$settings = get_settings();

$statusFilter = $_GET['status'] ?? '';
$doctorFilter = $_GET['doctor'] ?? '';
$serviceFilter = $_GET['service'] ?? '';
$dateFilter = $_GET['date'] ?? '';

$filtered = get_filtered_bookings(
    $statusFilter ?: null,
    $doctorFilter ?: null,
    $serviceFilter ?: null,
    $dateFilter ?: null
);

$stats = get_stats();
$byService = get_bookings_by_service();

$weekdaySelected = $schedule['hours']['weekday'] ?? [];
$saturdaySelected = $schedule['hours']['saturday'] ?? [];
$weekdayOptions = admin_time_options($weekdaySelected);
$saturdayOptions = admin_time_options($saturdaySelected);
$exceptions = $schedule['exceptions'] ?? [];

function h(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function render_time_blocks(string $name, array $options, array $selected): void {
    $selectedMap = array_fill_keys($selected, true);
    echo '<div class="time-blocks" role="group">';
    foreach ($options as $time) {
        $id = preg_replace('/\W+/', '_', $name . '_' . $time);
        $checked = isset($selectedMap[$time]) ? ' checked' : '';
        $active = isset($selectedMap[$time]) ? ' is-active' : '';
        echo '<label class="time-block' . $active . '" for="' . h($id) . '">';
        echo '<input type="checkbox" id="' . h($id) . '" name="' . h($name) . '[]" value="' . h($time) . '"' . $checked . '>';
        echo '<span>' . h($time) . '</span>';
        echo '</label>';
    }
    echo '</div>';
}

function doctor_photo_src(string $photo): string {
    if ($photo === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $photo) || str_starts_with($photo, 'data:')) {
        return $photo;
    }
    return '../' . ltrim($photo, '/');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Админ-панель — Зубр</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="admin">
    <aside class="sidebar">
      <div class="sidebar__top">
        <p class="brand">Зубр</p>
        <p class="sidebar__tag">Клиника · админ</p>
      </div>
      <nav class="sidebar__nav" aria-label="Разделы">
        <a class="<?= $section === 'bookings' ? 'is-active' : '' ?>" href="?section=bookings">Заявки</a>
        <a class="<?= $section === 'services' ? 'is-active' : '' ?>" href="?section=services">Услуги</a>
        <a class="<?= $section === 'doctors' ? 'is-active' : '' ?>" href="?section=doctors">Врачи</a>
        <a class="<?= $section === 'schedule' ? 'is-active' : '' ?>" href="?section=schedule">Расписание</a>
        <a class="<?= $section === 'reviews' ? 'is-active' : '' ?>" href="?section=reviews">Отзывы</a>
        <a class="<?= $section === 'settings' ? 'is-active' : '' ?>" href="?section=settings">Настройки</a>
        <a class="<?= $section === 'stats' ? 'is-active' : '' ?>" href="?section=stats">Статистика</a>
      </nav>
      <div class="sidebar__footer">
        <div class="admin-chip">
          <span class="admin-chip__avatar" aria-hidden="true"><?= h(function_exists('mb_substr') ? mb_strtoupper(mb_substr($adminName, 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($adminName, 0, 1))) ?></span>
          <div class="admin-chip__meta">
            <span class="admin-chip__label">Администратор</span>
            <strong class="admin-chip__name"><?= h($adminName) ?></strong>
          </div>
        </div>
        <a class="btn-logout" href="?logout=1">Выйти</a>
      </div>
    </aside>

    <main class="content">
      <?php if ($flash): ?><p class="flash"><?= h($flash) ?></p><?php endif; ?>

      <?php if ($section === 'bookings'): ?>
        <h1>Заявки на запись</h1>
        <form class="filters" method="get">
          <input type="hidden" name="section" value="bookings">
          <div class="select-wrap">
            <select name="status" aria-label="Статус">
              <option value="">Все статусы</option>
              <?php foreach (['новая' => 'Новая', 'подтверждена' => 'Подтверждена', 'завершена' => 'Завершена', 'отменена' => 'Отменена'] as $st => $label): ?>
                <option value="<?= h($st) ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= h($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <input type="date" name="date" value="<?= h($dateFilter) ?>">
          <div class="select-wrap">
            <select name="doctor" aria-label="Врач">
              <option value="">Все врачи</option>
              <?php foreach ($doctors as $d): ?>
                <option value="<?= h($d['name'] ?? '') ?>" <?= $doctorFilter === ($d['name'] ?? '') ? 'selected' : '' ?>><?= h($d['name'] ?? '') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="select-wrap">
            <select name="service" aria-label="Услуга">
              <option value="">Все услуги</option>
              <?php foreach ($services as $s): ?>
                <option value="<?= h($s['name'] ?? '') ?>" <?= $serviceFilter === ($s['name'] ?? '') ? 'selected' : '' ?>><?= h($s['name'] ?? '') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit">Фильтр</button>
        </form>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Дата</th><th>Пациент</th><th>Услуга / врач</th><th>Жалоба</th><th>Статус</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$filtered): ?>
                <tr><td colspan="5">Заявок пока нет</td></tr>
              <?php endif; ?>
              <?php foreach ($filtered as $b): ?>
                <tr>
                  <td><?= h(($b['date'] ?? '') . ' ' . ($b['time'] ?? '')) ?></td>
                  <td>
                    <strong><?= h($b['name'] ?? '') ?></strong><br>
                    <?= h($b['phone'] ?? '') ?>
                  </td>
                  <td>
                    <?= h($b['service'] ?: '—') ?><br>
                    <?= h($b['doctor'] ?: '—') ?>
                  </td>
                  <td><?= h($b['complaint'] ?? '') ?></td>
                  <td>
                    <form method="post" class="inline">
                      <input type="hidden" name="action" value="update_booking_status">
                      <input type="hidden" name="id" value="<?= h($b['id'] ?? '') ?>">
                      <div class="select-wrap select-wrap--compact">
                        <select name="status" onchange="this.form.submit()" aria-label="Статус заявки">
                          <?php foreach (['новая' => 'Новая', 'подтверждена' => 'Подтверждена', 'завершена' => 'Завершена', 'отменена' => 'Отменена'] as $st => $label): ?>
                            <option value="<?= h($st) ?>" <?= ($b['status'] ?? '') === $st ? 'selected' : '' ?>><?= h($label) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <?php if ($section === 'services'): ?>
        <h1>Услуги</h1>
        <form method="post" class="stack">
          <input type="hidden" name="action" value="save_services">
          <?php foreach ($services as $s): $id = $s['id']; ?>
            <fieldset>
              <legend><?= h($s['name'] ?? '') ?></legend>
              <label>Название<input name="name[<?= h($id) ?>]" value="<?= h($s['name'] ?? '') ?>"></label>
              <label>Кратко<textarea name="short[<?= h($id) ?>]" rows="2"><?= h($s['short'] ?? '') ?></textarea></label>
              <label>Цена<input name="price[<?= h($id) ?>]" value="<?= h($s['price'] ?? '') ?>"></label>
              <label>Длительность<input name="duration[<?= h($id) ?>]" value="<?= h($s['duration'] ?? '') ?>"></label>
              <label>Описание<textarea name="description[<?= h($id) ?>]" rows="3"><?= h($s['description'] ?? '') ?></textarea></label>
            </fieldset>
          <?php endforeach; ?>
          <button type="submit">Сохранить услуги</button>
        </form>
      <?php endif; ?>

      <?php if ($section === 'doctors'): ?>
        <h1>Врачи</h1>
        <form method="post" class="stack" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_doctors">
          <?php foreach ($doctors as $d):
            $id = $d['id'];
            $photo = (string)($d['photo'] ?? '');
            $photoSrc = doctor_photo_src($photo);
          ?>
            <fieldset>
              <legend><?= h($d['name'] ?? '') ?></legend>
              <label>ФИО<input name="name[<?= h($id) ?>]" value="<?= h($d['name'] ?? '') ?>"></label>
              <label>Специализация<input name="role[<?= h($id) ?>]" value="<?= h($d['role'] ?? '') ?>"></label>
              <label>Стаж<input name="experience[<?= h($id) ?>]" value="<?= h($d['experience'] ?? '') ?>"></label>
              <label>Чем занимается<textarea name="focus[<?= h($id) ?>]" rows="2"><?= h($d['focus'] ?? '') ?></textarea></label>
              <label>Что любит<textarea name="love[<?= h($id) ?>]" rows="2"><?= h($d['love'] ?? '') ?></textarea></label>
              <input type="hidden" name="photo[<?= h($id) ?>]" value="<?= h($photo) ?>">
              <div class="photo-field">
                <span class="photo-field__label">Фото врача</span>
                <div class="photo-field__row">
                  <div class="photo-preview<?= $photoSrc ? '' : ' is-empty' ?>">
                    <?php if ($photoSrc): ?>
                      <img src="<?= h($photoSrc) ?>" alt="Фото <?= h($d['name'] ?? '') ?>">
                    <?php else: ?>
                      <span>Нет фото</span>
                    <?php endif; ?>
                  </div>
                  <div class="photo-field__controls">
                    <label class="file-btn">
                      <input type="file" name="photo_file[<?= h($id) ?>]" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif">
                      <span>Выбрать файл</span>
                    </label>
                    <p class="muted">JPG, PNG, WEBP или GIF до 5 МБ</p>
                    <?php if ($photo): ?>
                      <p class="muted photo-path"><?= h($photo) ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </fieldset>
          <?php endforeach; ?>
          <button type="submit">Сохранить врачей</button>
        </form>
      <?php endif; ?>

      <?php if ($section === 'schedule'): ?>
        <h1>Расписание</h1>
        <form method="post" class="stack stack--wide" id="schedule-form">
          <input type="hidden" name="action" value="save_schedule">
          <fieldset>
            <legend>Рабочие дни</legend>
            <div class="day-checks">
              <?php
                $labels = [1=>'Пн',2=>'Вт',3=>'Ср',4=>'Чт',5=>'Пт',6=>'Сб',7=>'Вс'];
                $active = $schedule['weekdays'] ?? [1,2,3,4,5,6];
                foreach ($labels as $n => $label):
              ?>
                <label class="check"><input type="checkbox" name="day_<?= $n ?>" <?= in_array($n, $active, true) ? 'checked' : '' ?>> <?= $label ?></label>
              <?php endforeach; ?>
            </div>
          </fieldset>

          <fieldset>
            <legend>Слоты Пн–Пт</legend>
            <p class="hint">Нажмите на время, чтобы включить или выключить слот</p>
            <?php render_time_blocks('weekday_hours', $weekdayOptions, $weekdaySelected); ?>
          </fieldset>

          <fieldset>
            <legend>Слоты суббота</legend>
            <p class="hint">Нажмите на время, чтобы включить или выключить слот</p>
            <?php render_time_blocks('saturday_hours', $saturdayOptions, $saturdaySelected); ?>
          </fieldset>

          <fieldset>
            <legend>Исключения</legend>
            <p class="hint">Особый день или выходной. Добавьте дату и отметьте доступные слоты.</p>
            <div class="exceptions" id="exceptions-list">
              <?php foreach ($exceptions as $i => $ex):
                $exSlots = $ex['slots'] ?? [];
                $isOff = empty($exSlots);
                $exOptions = admin_time_options($exSlots ?: $weekdaySelected);
              ?>
                <div class="exception-row" data-exception>
                  <div class="exception-row__head">
                    <label>Дата
                      <input type="date" name="ex_date[<?= (int)$i ?>]" value="<?= h($ex['date'] ?? '') ?>" required>
                    </label>
                    <label class="check">
                      <input type="checkbox" name="ex_off[<?= (int)$i ?>]" value="1" <?= $isOff ? 'checked' : '' ?> data-ex-off>
                      Выходной
                    </label>
                    <button type="button" class="btn-ghost" data-remove-exception>Удалить</button>
                  </div>
                  <div class="exception-row__slots" data-ex-slots <?= $isOff ? 'hidden' : '' ?>>
                    <?php render_time_blocks('ex_slots[' . (int)$i . ']', $exOptions, $exSlots); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="btn-ghost" id="add-exception">+ Добавить исключение</button>
          </fieldset>

          <button type="submit">Сохранить расписание</button>
        </form>

        <template id="exception-template">
          <div class="exception-row" data-exception>
            <div class="exception-row__head">
              <label>Дата
                <input type="date" name="ex_date[__INDEX__]" value="" required>
              </label>
              <label class="check">
                <input type="checkbox" name="ex_off[__INDEX__]" value="1" data-ex-off>
                Выходной
              </label>
              <button type="button" class="btn-ghost" data-remove-exception>Удалить</button>
            </div>
            <div class="exception-row__slots" data-ex-slots>
              <?php render_time_blocks('ex_slots[__INDEX__]', $weekdayOptions, []); ?>
            </div>
          </div>
        </template>
      <?php endif; ?>

      <?php if ($section === 'reviews'): ?>
        <h1>Отзывы</h1>
        <form method="post" class="stack">
          <input type="hidden" name="action" value="save_reviews">
          <?php foreach ($reviews as $r): $id = $r['id']; ?>
            <fieldset>
              <legend><?= h($r['author'] ?? '') ?></legend>
              <label>Автор<input name="author[<?= h($id) ?>]" value="<?= h($r['author'] ?? '') ?>"></label>
              <label>Текст<textarea name="text[<?= h($id) ?>]" rows="3"><?= h($r['text'] ?? '') ?></textarea></label>
              <label>Дата<input type="date" name="date[<?= h($id) ?>]" value="<?= h($r['date'] ?? '') ?>"></label>
              <label>Порядок<input type="number" name="order[<?= h($id) ?>]" value="<?= (int)($r['sort_order'] ?? 0) ?>"></label>
              <label class="check"><input type="checkbox" name="visible[<?= h($id) ?>]" <?= !empty($r['is_visible']) ? 'checked' : '' ?>> Показывать</label>
            </fieldset>
          <?php endforeach; ?>
          <fieldset>
            <legend>Новый отзыв</legend>
            <label>Автор<input name="new_author"></label>
            <label>Текст<textarea name="new_text" rows="3"></textarea></label>
            <label>Дата<input type="date" name="new_date" value="<?= date('Y-m-d') ?>"></label>
          </fieldset>
          <button type="submit">Сохранить отзывы</button>
        </form>
      <?php endif; ?>

      <?php if ($section === 'settings'): ?>
        <h1>Настройки сайта</h1>
        <form method="post" class="stack">
          <input type="hidden" name="action" value="save_settings">
          <label>Название<input name="clinic_name" value="<?= h($settings['clinic_name'] ?? '') ?>"></label>
          <label>Заголовок главного экрана<input name="hero_title" value="<?= h($settings['hero_title'] ?? '') ?>"></label>
          <label>Подзаголовок<textarea name="hero_text" rows="3"><?= h($settings['hero_text'] ?? '') ?></textarea></label>
          <label>Телефон<input name="phone" value="<?= h($settings['phone'] ?? '') ?>"></label>
          <label>Email<input name="email" value="<?= h($settings['email'] ?? '') ?>"></label>
          <label>Адрес<input name="address" value="<?= h($settings['address'] ?? '') ?>"></label>
          <label>Режим работы<textarea name="hours" rows="2"><?= h($settings['hours'] ?? '') ?></textarea></label>
          <label>ИНН<input name="inn" value="<?= h($settings['inn'] ?? '') ?>"></label>
          <label>Telegram<input name="telegram" value="<?= h($settings['telegram'] ?? '') ?>"></label>
          <label>WhatsApp<input name="whatsapp" value="<?= h($settings['whatsapp'] ?? '') ?>"></label>
          <label>Email для уведомлений<input name="admin_email" value="<?= h($settings['admin_email'] ?? '') ?>"></label>
          <label>Код аналитики<textarea name="analytics" rows="3" placeholder="Яндекс.Метрика"><?= h($settings['analytics'] ?? '') ?></textarea></label>
          <button type="submit">Сохранить настройки</button>
        </form>
      <?php endif; ?>

      <?php if ($section === 'uploads'): ?>
        <h1>Загрузка изображений</h1>
        <form method="post" enctype="multipart/form-data" class="stack">
          <input type="hidden" name="action" value="upload_image">
          <div class="photo-field">
            <span class="photo-field__label">Файл (jpg, png, webp до 5 МБ)</span>
            <label class="file-btn">
              <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif" required>
              <span>Выбрать файл</span>
            </label>
          </div>
          <button type="submit">Загрузить</button>
        </form>
        <p class="muted">После загрузки путь вида <code>uploads/имя_файла.jpg</code> можно использовать на сайте.</p>
        <ul class="files">
          <?php
            $files = get_uploaded_files();
            foreach ($files as $f):
          ?>
            <li>uploads/<?= h($f) ?></li>
          <?php endforeach; ?>
          <?php if (!$files): ?><li>Пока нет загруженных файлов</li><?php endif; ?>
        </ul>
      <?php endif; ?>

      <?php if ($section === 'stats'): ?>
        <h1>Статистика заявок</h1>
        <div class="stats">
          <div class="stat"><strong><?= (int)$stats['total'] ?></strong><span>Всего</span></div>
          <div class="stat"><strong><?= (int)$stats['new'] ?></strong><span>Новые</span></div>
          <div class="stat"><strong><?= (int)$stats['confirmed'] ?></strong><span>Подтверждены</span></div>
          <div class="stat"><strong><?= (int)$stats['done'] ?></strong><span>Завершены</span></div>
        </div>
        <h2>По услугам</h2>
        <ul class="stat-list">
          <?php foreach ($byService as $name => $count): ?>
            <li><span><?= h($name) ?></span><strong><?= (int)$count ?></strong></li>
          <?php endforeach; ?>
          <?php if (!$byService): ?><li>Пока нет данных</li><?php endif; ?>
        </ul>
      <?php endif; ?>
    </main>
  </div>

  <script>
    (function () {
      document.querySelectorAll('.time-blocks').forEach(function (grid) {
        grid.addEventListener('change', function (e) {
          var input = e.target;
          if (!(input instanceof HTMLInputElement) || input.type !== 'checkbox') return;
          var label = input.closest('.time-block');
          if (label) label.classList.toggle('is-active', input.checked);
        });
      });

      document.querySelectorAll('.file-btn input[type="file"]').forEach(function (input) {
        input.addEventListener('change', function () {
          var label = input.closest('.file-btn');
          var span = label && label.querySelector('span');
          if (!span) return;
          span.textContent = input.files && input.files[0] ? input.files[0].name : 'Выбрать файл';
        });
      });

      var list = document.getElementById('exceptions-list');
      var tpl = document.getElementById('exception-template');
      var addBtn = document.getElementById('add-exception');
      var nextIndex = <?= count($exceptions) ?>;

      function bindExceptionRow(row) {
        var off = row.querySelector('[data-ex-off]');
        var slots = row.querySelector('[data-ex-slots]');
        var remove = row.querySelector('[data-remove-exception]');
        if (off && slots) {
          off.addEventListener('change', function () {
            slots.hidden = off.checked;
          });
        }
        if (remove) {
          remove.addEventListener('click', function () {
            row.remove();
          });
        }
      }

      if (list) {
        list.querySelectorAll('[data-exception]').forEach(bindExceptionRow);
      }

      if (addBtn && tpl && list) {
        addBtn.addEventListener('click', function () {
          var html = tpl.innerHTML.replace(/__INDEX__/g, String(nextIndex++));
          var wrap = document.createElement('div');
          wrap.innerHTML = html.trim();
          var row = wrap.firstElementChild;
          if (!row) return;
          list.appendChild(row);
          bindExceptionRow(row);
          row.querySelectorAll('.time-blocks').forEach(function (grid) {
            grid.addEventListener('change', function (e) {
              var input = e.target;
              if (!(input instanceof HTMLInputElement) || input.type !== 'checkbox') return;
              var label = input.closest('.time-block');
              if (label) label.classList.toggle('is-active', input.checked);
            });
          });
        });
      }

      function enhanceSelect(wrap) {
        var select = wrap.querySelector('select');
        if (!select || wrap.classList.contains('is-enhanced')) return;
        wrap.classList.add('is-enhanced');

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'select-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        var ariaLabel = select.getAttribute('aria-label');
        if (ariaLabel) trigger.setAttribute('aria-label', ariaLabel);

        var label = document.createElement('span');
        label.className = 'select-trigger__label';
        var arrow = document.createElement('span');
        arrow.className = 'select-trigger__arrow';
        arrow.setAttribute('aria-hidden', 'true');
        trigger.appendChild(label);
        trigger.appendChild(arrow);

        var menu = document.createElement('ul');
        menu.className = 'select-menu';
        menu.setAttribute('role', 'listbox');

        var options = Array.prototype.slice.call(select.options);
        var items = options.map(function (opt, i) {
          var li = document.createElement('li');
          li.className = 'select-menu__option';
          li.setAttribute('role', 'option');
          li.setAttribute('data-index', String(i));
          li.setAttribute('aria-selected', opt.selected ? 'true' : 'false');
          li.textContent = opt.textContent;
          menu.appendChild(li);
          return li;
        });

        function updateLabel() {
          var opt = select.options[select.selectedIndex];
          label.textContent = opt ? opt.textContent : '';
        }
        updateLabel();

        function selectIndex(i, fireChange) {
          items.forEach(function (li, idx) {
            li.setAttribute('aria-selected', idx === i ? 'true' : 'false');
          });
          select.selectedIndex = i;
          updateLabel();
          if (fireChange) select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function clearFocus() {
          items.forEach(function (li) { li.classList.remove('is-focused'); });
        }

        function openMenu() {
          document.querySelectorAll('.select-wrap.is-open').forEach(function (w) {
            if (w !== wrap) { w.classList.remove('is-open'); w.querySelector('.select-trigger').setAttribute('aria-expanded', 'false'); }
          });
          wrap.classList.add('is-open');
          trigger.setAttribute('aria-expanded', 'true');
          clearFocus();
          var current = items[select.selectedIndex];
          if (current) {
            current.classList.add('is-focused');
            current.scrollIntoView({ block: 'nearest' });
          }
        }

        function closeMenu() {
          wrap.classList.remove('is-open');
          trigger.setAttribute('aria-expanded', 'false');
          clearFocus();
        }

        trigger.addEventListener('click', function () {
          if (wrap.classList.contains('is-open')) closeMenu(); else openMenu();
        });

        items.forEach(function (li, i) {
          li.addEventListener('click', function () {
            selectIndex(i, true);
            closeMenu();
            trigger.focus();
          });
          li.addEventListener('mouseenter', function () {
            clearFocus();
            li.classList.add('is-focused');
          });
        });

        trigger.addEventListener('keydown', function (e) {
          var open = wrap.classList.contains('is-open');
          if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if (!open) { openMenu(); return; }
            var focused = menu.querySelector('.is-focused');
            var idx = focused ? Number(focused.getAttribute('data-index')) : select.selectedIndex;
            idx = e.key === 'ArrowDown' ? Math.min(idx + 1, items.length - 1) : Math.max(idx - 1, 0);
            clearFocus();
            items[idx].classList.add('is-focused');
            items[idx].scrollIntoView({ block: 'nearest' });
          } else if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            if (open) {
              var f = menu.querySelector('.is-focused');
              if (f) selectIndex(Number(f.getAttribute('data-index')), true);
              closeMenu();
            } else {
              openMenu();
            }
          } else if (e.key === 'Escape') {
            closeMenu();
          }
        });

        wrap.appendChild(trigger);
        wrap.appendChild(menu);
      }

      document.addEventListener('click', function (e) {
        document.querySelectorAll('.select-wrap.is-open').forEach(function (w) {
          if (!w.contains(e.target)) {
            w.classList.remove('is-open');
            var t = w.querySelector('.select-trigger');
            if (t) t.setAttribute('aria-expanded', 'false');
          }
        });
      });

      document.querySelectorAll('.select-wrap').forEach(enhanceSelect);
    })();
  </script>
</body>
</html>