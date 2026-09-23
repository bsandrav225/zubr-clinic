<?php
$BASE = '';
require_once __DIR__ . '/includes/boot.php';

$selectedService = $_GET['service'] ?? '';
$pageTitle = 'Зубр — стоматологическая клиника в Москве | Диагностика, лечение, имплантация';
$pageDescription = 'Клиника «Зубр» с 2020 года: КТ, лечение под микроскопом, своя лаборатория, имплантация, протезирование, детский приём и лечение во сне. Онлайн-запись.';
$pageCanonical = SITE_URL . '/';
$activeNav = 'home';

$ld = [
  '@context' => 'https://schema.org',
  '@type' => 'Dentist',
  'name' => SITE_NAME,
  'image' => SITE_OG_IMAGE,
  'url' => SITE_URL . '/',
  'telephone' => $phone,
  'email' => $email,
  'priceRange' => '₽₽',
  'address' => [
    '@type' => 'PostalAddress',
    'streetAddress' => $address,
    'addressCountry' => 'RU',
  ],
  'sameAs' => array_values(array_filter([$telegram, $whatsapp])),
];
$extraHead = '<script type="application/ld+json">' . json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';

$bookingServices = zubr_booking_services();
$visibleReviews = array_values(array_filter($reviews, static function ($r) {
    return !empty($r['is_visible']) || !empty($r['visible']) || !isset($r['is_visible']);
}));

$serviceIcons = [
    'Лечение зубов' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3c2.5 0 4 2 4 4.5S14 14 12 21c-2-7-4-10.5-4-13.5S9.5 3 12 3Z"/><path d="M9 9.5h6"/></svg>',
    'Удаление зубов' => '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 4h8l1 4-3 2v9l-2 2-2-2V10L7 8l1-4Z"/></svg>',
    'Чистка зубов' => '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 14c2-4 5-6 8-6s6 2 8 6"/><path d="M8 14v3M12 14v5M16 14v3"/></svg>',
    'Отбеливание' => '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2"/></svg>',
    'Детский приём' => '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="3.5"/><path d="M6 20c1.5-3.5 3.5-5 6-5s4.5 1.5 6 5"/></svg>',
    'Имплантация зубов' => '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v10"/><path d="M8 7h8"/><path d="M9 13v8h6v-8"/></svg>',
    'Протезирование зубов' => '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 14c2-6 4.5-9 8-9s6 3 8 9"/><path d="M7 14v4M12 14v6M17 14v4"/></svg>',
];

require __DIR__ . '/includes/header.php';
?>
  <main id="main">
    <section class="hero" id="top">
      <div class="hero__glow" aria-hidden="true"></div>
      <div class="container hero__grid">
        <div class="hero__copy">
          <p class="hero__kicker reveal">Стоматология в Москве · с <?= (int)$clinic['since'] ?> года</p>
          <p class="hero__brand reveal">Зубр</p>
          <h1 class="hero__title reveal reveal-d1"><?= htmlspecialchars($hero_title) ?></h1>
          <p class="hero__text reveal reveal-d2"><?= htmlspecialchars($hero_text) ?></p>
          <div class="hero__actions reveal reveal-d3">
            <a class="btn btn--primary btn--lg" href="#booking">Записаться на приём</a>
            <a class="btn btn--ghost btn--lg" href="#map">Карта зубов</a>
            <a class="btn btn--ghost btn--lg" href="about.php">О клинике</a>
          </div>
        </div>

        <div class="hero__visual reveal reveal-d2">
          <div class="hero__frame">
            <img
              src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1400&q=80"
              alt="Стоматологический кабинет клиники Зубр"
              width="1400"
              height="1050"
              fetchpriority="high"
            >
          </div>
        </div>
      </div>
    </section>

    <section class="stats-row" aria-label="О клинике в цифрах">
      <div class="container">
        <ul class="stats-row__list">
          <?php foreach ($clinic['stats'] as $stat): ?>
            <li>
              <strong><?= htmlspecialchars($stat['value']) ?></strong>
              <div>
                <b><?= htmlspecialchars($stat['label']) ?></b>
                <span><?= htmlspecialchars($stat['note'] ?? '') ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <section class="trust-bar">
      <div class="container trust-bar__grid">
        <article class="trust-card">
          <strong>Полный цикл</strong>
          <p>Диагностика, лечение и ортопедия в одном месте — со своей лабораторией.</p>
        </article>
        <article class="trust-card">
          <strong>Цифровой протокол</strong>
          <p>КТ и микроскоп, чтобы видеть каналы, кость и скрытый кариес.</p>
        </article>
        <article class="trust-card">
          <strong>План до начала</strong>
          <p>Этапы и стоимость фиксируем заранее. Есть рассрочка на лечение.</p>
        </article>
        <article class="trust-card">
          <strong>Гарантия</strong>
          <p>Отвечаем за результат в соответствии с условиями договора.</p>
        </article>
      </div>
    </section>

    <?php require __DIR__ . '/includes/section-dentamap.php'; ?>

    <section class="section services" id="services">
      <div class="container">
        <div class="section__head section__head--wide">
          <p class="eyebrow">Каталог</p>
          <h2 class="section__title">Услуги цифровой стоматологии</h2>
          <p class="section__lead">Выберите направление в меню или откройте карточку — внутри состав процедуры, показания и запись.</p>
        </div>
        <div class="services__bento">
          <?php
            $homeSlugs = ['treatment', 'implant', 'prosthetics', 'cleaning', 'extraction', 'kids', 'whitening', 'sedation'];
            foreach ($homeSlugs as $slug):
              $service = $clinic['services'][$slug] ?? null;
              if (!$service) continue;
              $wide = in_array($slug, ['implant', 'kids'], true);
              $lg = $slug === 'treatment';
              $ghost = $slug === 'implant';
          ?>
            <a class="tile<?= $wide ? ' tile--wide' : '' ?><?= $lg ? ' tile--lg' : '' ?><?= $ghost ? ' tile--ghost' : '' ?>"
               href="<?= htmlspecialchars(zubr_service_url($slug, $BASE)) ?>">
              <span class="tile__icon" aria-hidden="true"><?= $serviceIcons[$service['name']] ?? $serviceIcons['Лечение зубов'] ?></span>
              <span class="tile__name"><?= htmlspecialchars($service['name']) ?></span>
              <span class="tile__desc"><?= htmlspecialchars($service['short']) ?></span>
              <span class="tile__foot">
                <span class="tile__price"><?= htmlspecialchars($service['price']) ?></span>
                <span class="tile__go" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
        <p style="margin-top:1.2rem">
          <a class="btn btn--soft" href="services/">Все услуги и описания процедур</a>
          <a class="btn btn--ghost" href="prices.php">Смотреть цены</a>
        </p>
      </div>
    </section>

    <section class="section process" id="process">
      <div class="container">
        <div class="section__head">
          <p class="eyebrow">Первый визит</p>
          <h2 class="section__title">Как проходит лечение</h2>
          <p class="section__lead">Неважно, насколько обширны интернет-знания о методах. Важно пройти обследование и составить план под ваш случай.</p>
        </div>
        <div class="process__grid">
          <?php foreach ($clinic['process'] as $step): ?>
            <article class="process-card">
              <p class="process-card__n"><?= htmlspecialchars($step['step']) ?></p>
              <h3><?= htmlspecialchars($step['title']) ?></h3>
              <p><?= htmlspecialchars($step['text']) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section doctors" id="doctors">
      <div class="container">
        <div class="section__head">
          <p class="eyebrow">Команда</p>
          <h2 class="section__title">Врачи</h2>
          <p class="section__lead">Можно записаться к конкретному специалисту одной кнопкой — форма ниже уже подставит имя.</p>
        </div>
        <div class="doctors__list">
          <?php foreach ($doctors as $doctor): ?>
            <?php if (isset($doctor['visible']) && empty($doctor['visible'])) continue; ?>
            <article class="doctor">
              <div class="doctor__photo">
                <img src="<?= htmlspecialchars($doctor['photo'] ?? '') ?>"
                     alt="<?= htmlspecialchars($doctor['name']) ?>"
                     width="900" height="1100" loading="lazy">
              </div>
              <div class="doctor__body">
                <h3><?= htmlspecialchars($doctor['name']) ?></h3>
                <p class="doctor__role"><?= htmlspecialchars($doctor['role'] ?? '') ?></p>
                <ul class="doctor__meta">
                  <li>
                    <span class="ico" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                    <span><strong>Стаж:</strong> <?= htmlspecialchars($doctor['experience'] ?? '') ?></span>
                  </li>
                  <li>
                    <span class="ico" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19h16M7 16V8l5-4 5 4v8"/></svg></span>
                    <span><strong>Чем занимается:</strong> <?= htmlspecialchars($doctor['focus'] ?? '') ?></span>
                  </li>
                  <li>
                    <span class="ico" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z"/></svg></span>
                    <span><strong>Что любит:</strong> <?= htmlspecialchars($doctor['love'] ?? '') ?></span>
                  </li>
                </ul>
                <button class="btn btn--primary" type="button" data-book-doctor="<?= htmlspecialchars($doctor['name']) ?>">Записаться к этому врачу</button>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section booking" id="booking">
      <div class="container">
        <div class="booking__shell">
          <div class="booking__intro">
            <p class="eyebrow eyebrow--on-dark">Онлайн-запись</p>
            <h2 class="section__title">Запись на приём</h2>
            <p class="section__lead">Выберите дату в мини-календаре и свободный слот. Администратор подтвердит заявку в рабочее время.</p>
            <ul class="booking__points">
              <li>
                <span aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7 10 17l-5-5"/></svg></span>
                Только доступные даты и время
              </li>
              <li>
                <span aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7 10 17l-5-5"/></svg></span>
                Можно выбрать врача заранее
              </li>
              <li>
                <span aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7 10 17l-5-5"/></svg></span>
                Рассрочка при лечении в несколько визитов
              </li>
            </ul>
          </div>

          <form class="form" id="booking-form" action="api/booking.php" method="POST" novalidate>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

              <div class="form__grid">
                  <label class="field">
                      <span class="field__label">Имя <abbr title="обязательно">*</abbr></span>
                      <input type="text" name="name" autocomplete="name" required placeholder="Как к вам обращаться" maxlength="100">
                      <span class="field__error" data-error="name"></span>
                  </label>
                  <label class="field">
                      <span class="field__label">Телефон <abbr title="обязательно">*</abbr></span>
                      <input type="tel" name="phone" autocomplete="tel" required placeholder="+7 (___) ___-__-__" inputmode="tel" maxlength="20">
                      <span class="field__error" data-error="phone"></span>
                  </label>
              </div>

              <label class="field">
                  <span class="field__label">Что беспокоит <abbr title="обязательно">*</abbr></span>
                  <textarea name="complaint" rows="3" required placeholder="Кратко опишите жалобу или причину визита" maxlength="1000"></textarea>
                  <span class="field__error" data-error="complaint"></span>
              </label>

              <div class="schedule" data-schedule>
                  <div class="calendar" data-calendar>
                      <div class="calendar__head">
                          <button type="button" class="icon-btn" data-cal-prev aria-label="Предыдущий месяц">
                              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                          </button>
                          <p class="calendar__title" data-cal-title>Сентябрь 2026</p>
                          <button type="button" class="icon-btn" data-cal-next aria-label="Следующий месяц">
                              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                          </button>
                      </div>
                      <div class="calendar__weekdays" aria-hidden="true">
                          <span>Пн</span><span>Вт</span><span>Ср</span><span>Чт</span><span>Пт</span><span>Сб</span><span>Вс</span>
                      </div>
                      <div class="calendar__grid" data-cal-grid role="grid" aria-label="Календарь выбора даты"></div>
                      <p class="calendar__hint">Воскресенье — выходной. Прошедшие даты недоступны.</p>
                  </div>

                  <div class="slots">
                      <p class="slots__label">Удобное время <abbr title="обязательно">*</abbr></p>
                      <p class="slots__selected" data-selected-date>Сначала выберите дату в календаре</p>
                      <div class="slots__grid" data-time-slots></div>
                      <span class="field__error" data-error="time"></span>
                  </div>
              </div>

              <input type="hidden" name="date" data-date-input required value="">
              <input type="hidden" name="time" data-time-input required value="">

              <div class="form__grid">
                  <label class="field">
                      <span class="field__label">Услуга</span>
                      <select name="service" data-service-select>
                          <option value="">Не выбрано</option>
                          <?php foreach ($bookingServices as $service): ?>
                              <option value="<?= htmlspecialchars($service['name']) ?>" <?= ($selectedService === $service['name']) ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($service['name']) ?>
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </label>
                  <label class="field">
                      <span class="field__label">Врач</span>
                      <select name="doctor" data-doctor-select>
                          <option value="">Не выбран</option>
                          <?php foreach ($doctors as $doctor): ?>
                              <option value="<?= htmlspecialchars($doctor['name']) ?>"><?= htmlspecialchars($doctor['name']) ?></option>
                          <?php endforeach; ?>
                      </select>
                  </label>
              </div>

              <label class="field">
                  <span class="field__label">Комментарий</span>
                  <textarea name="comment" rows="2" placeholder="Дополнительная информация для администратора" maxlength="500"></textarea>
              </label>

              <label class="check">
                  <input type="checkbox" name="consent" required>
                  <span>Согласен(на) на <a href="consent.html">обработку персональных данных</a></span>
                  <span class="field__error" data-error="consent"></span>
              </label>

              <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">

              <button class="btn btn--primary btn--lg form__submit" type="submit" id="form-submit">Отправить заявку</button>
              <p class="form__status" data-form-status role="status" aria-live="polite"></p>
          </form>
        </div>
      </div>
    </section>

    <section class="section reviews" id="reviews">
      <div class="container">
        <div class="section__head section__head--row">
          <div>
            <p class="eyebrow">Пациенты</p>
            <h2 class="section__title">Отзывы</h2>
          </div>
          <div class="reviews__nav">
            <button class="icon-btn" type="button" data-reviews-prev aria-label="Назад">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button class="icon-btn" type="button" data-reviews-next aria-label="Вперёд">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </button>
          </div>
        </div>

        <div class="reviews__scroller" data-reviews-scroller>
          <?php foreach ($visibleReviews as $review): ?>
            <article class="review">
              <div class="review__stars" aria-label="5 из 5">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <svg viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path fill="currentColor" d="m12 3 2.5 5.2 5.7.8-4.1 4 1 5.7L12 16.1 6.9 18.7l1-5.7-4.1-4 5.7-.8L12 3Z"/></svg>
                <?php endfor; ?>
              </div>
              <p><?= htmlspecialchars($review['text']) ?></p>
              <footer>
                <strong><?= htmlspecialchars($review['author']) ?></strong>
                <time datetime="<?= htmlspecialchars($review['date']) ?>">
                  <?= htmlspecialchars(date('d.m.Y', strtotime((string)$review['date']))) ?>
                </time>
              </footer>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section" id="faq-preview">
      <div class="container">
        <div class="section__head section__head--row">
          <div>
            <p class="eyebrow">Коротко</p>
            <h2 class="section__title">Частые вопросы</h2>
          </div>
          <a class="btn btn--soft" href="faq.php">Все вопросы</a>
        </div>
        <div class="faq-list">
          <?php foreach (array_slice($clinic['faq'], 0, 5) as $item): ?>
            <details class="faq-item">
              <summary><?= htmlspecialchars($item['q']) ?></summary>
              <p><?= htmlspecialchars($item['a']) ?></p>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section contacts" id="contacts">
      <div class="container contacts__grid">
        <div>
          <p class="eyebrow">Как нас найти</p>
          <h2 class="section__title">Контакты</h2>
          <p class="section__lead">Адрес, телефон и карта проезда. На мобильном — маршрут в один тап.</p>

          <ul class="contacts__list">
            <li>
              <span class="ico" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
              <div><strong>Адрес</strong><p><?= htmlspecialchars($address) ?></p></div>
            </li>
            <li>
              <span class="ico" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6.5 4h3l1.5 4-2 1.5a12 12 0 0 0 5.5 5.5L16 13.5l4 1.5v3A2 2 0 0 1 18 20 14 14 0 0 1 4 6a2 2 0 0 1 2.5-2Z"/></svg></span>
              <div><strong>Телефон</strong><p><a href="tel:<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></p></div>
            </li>
            <li>
              <span class="ico" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
              <div><strong>Email</strong><p><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></p></div>
            </li>
            <li>
              <span class="ico" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
              <div><strong>Режим работы</strong><p><?= zubr_hours_html($hours) ?></p></div>
            </li>
          </ul>

          <div class="contacts__actions">
            <a class="btn btn--primary" href="tel:<?= htmlspecialchars($phone_href) ?>">Позвонить</a>
            <a class="btn btn--soft" href="https://yandex.ru/maps/" target="_blank" rel="noopener noreferrer">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z"/><path d="M9 10h6M12 7v6"/></svg>
              Построить маршрут
            </a>
          </div>
        </div>

        <div class="contacts__map">
          <iframe
            title="Карта проезда к клинике Зубр"
            src="https://yandex.ru/map-widget/v1/?ll=37.617635%2C55.755814&z=15&l=map&pt=37.617635,55.755814,pm2rdm"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </section>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
