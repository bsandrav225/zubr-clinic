<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Зубр — стоматологическая клиника в Москве';
$pageDescription = $pageDescription ?? 'Клиника «Зубр»: диагностика, лечение, имплантация, протезирование и детский приём. Запишитесь онлайн.';
$pageCanonical = $pageCanonical ?? (SITE_URL . '/');
$activeNav = $activeNav ?? '';
$extraHead = $extraHead ?? '';
$selectedService = $selectedService ?? ($_GET['service'] ?? '');
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta name="robots" content="<?= htmlspecialchars($pageRobots ?? 'index, follow') ?>">
  <meta name="theme-color" content="#163a54">
  <link rel="canonical" href="<?= htmlspecialchars($pageCanonical) ?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= htmlspecialchars(SITE_NAME) ?>">
  <meta property="og:locale" content="ru_RU">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($pageCanonical) ?>">
  <meta property="og:image" content="<?= SITE_OG_IMAGE ?>">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta name="twitter:image" content="<?= SITE_OG_IMAGE ?>">

  <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($BASE) ?>favicon.svg">
  <link rel="alternate icon" href="<?= htmlspecialchars($BASE) ?>favicon.svg">
  <link rel="apple-touch-icon" href="<?= htmlspecialchars($BASE) ?>favicon.svg">

  <link rel="stylesheet" href="<?= htmlspecialchars($BASE) ?>css/styles.css">
  <?= $extraHead ?>
</head>
<body<?= $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass) . '"' : '' ?> data-base="<?= htmlspecialchars($BASE) ?>">
  <a class="skip-link" href="#main">Перейти к содержанию</a>

  <header class="header" data-header>
    <div class="container header__row">
      <a class="logo" href="<?= htmlspecialchars($BASE) ?>index.php" aria-label="Зубр — на главную">
        <span class="logo__mark" aria-hidden="true"><?= zubr_logo_svg(34) ?></span>
        <span class="logo__word">Зубр</span>
      </a>

      <nav class="nav" data-nav aria-label="Основная навигация">
        <div class="nav__item<?= $activeNav === 'services' ? ' is-current' : '' ?>" data-drop>
          <button class="nav__btn" type="button" aria-expanded="false" aria-haspopup="true">
            Услуги
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="nav__mega" role="menu">
            <div class="nav__mega-grid">
              <?php foreach ($clinic['categories'] as $cat): ?>
                <div class="nav__mega-col">
                  <p class="nav__mega-title"><?= htmlspecialchars($cat['title']) ?></p>
                  <p class="nav__mega-lead"><?= htmlspecialchars($cat['lead']) ?></p>
                  <?php foreach ($cat['items'] as $slug): ?>
                    <?php $item = $clinic['services'][$slug] ?? null; if (!$item) continue; ?>
                    <a href="<?= htmlspecialchars(zubr_service_url($slug, $BASE)) ?>">
                      <span><?= htmlspecialchars($item['name']) ?></span>
                      <small><?= htmlspecialchars($item['price']) ?></small>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="nav__mega-foot">
              <a href="<?= htmlspecialchars($BASE) ?>services/">Все услуги и описания процедур</a>
              <a class="btn btn--primary btn--sm" href="<?= htmlspecialchars($BASE) ?>index.php#map">Карта зубов</a>
            </div>
          </div>
        </div>
        <a class="<?= $activeNav === 'about' ? 'is-current' : '' ?>" href="<?= htmlspecialchars($BASE) ?>about.php">Клиника</a>
        <a href="<?= htmlspecialchars($BASE) ?>index.php#doctors">Врачи</a>
        <a class="<?= $activeNav === 'prices' ? 'is-current' : '' ?>" href="<?= htmlspecialchars($BASE) ?>prices.php">Цены</a>
        <a class="<?= $activeNav === 'faq' ? 'is-current' : '' ?>" href="<?= htmlspecialchars($BASE) ?>faq.php">Вопросы</a>
        <a href="<?= htmlspecialchars($BASE) ?>index.php#contacts">Контакты</a>
      </nav>

      <div class="header__right">
        <div class="header__phone">
          <a class="header__phone-link" href="tel:<?= htmlspecialchars($phone_href) ?>">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 4h3l1.5 4-2 1.5a12 12 0 0 0 5.5 5.5L16 13.5l4 1.5v3A2 2 0 0 1 18 20 14 14 0 0 1 4 6a2 2 0 0 1 2.5-2Z"/></svg>
            <span><?= htmlspecialchars($phone) ?></span>
          </a>
          <a class="btn btn--call" href="tel:<?= htmlspecialchars($phone_href) ?>">Позвонить</a>
        </div>

        <div class="messengers" aria-label="Мессенджеры">
          <a class="icon-btn" href="<?= htmlspecialchars($telegram) ?>" target="_blank" rel="noopener noreferrer" aria-label="Telegram">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M21.5 4.3 3.7 11.1c-1.2.5-1.2 1.2-.2 1.5l4.6 1.4 1.8 5.5c.2.7.4.9 1 .9.6 0 .9-.3 1.2-.6l2.7-2.6 4.5 3.3c.8.4 1.4.2 1.6-.8L22.9 5.5c.3-1.2-.4-1.7-1.4-1.2ZM9.6 14.1l9.1-5.7c.5-.3.9-.1.5.2l-7.4 6.7-.3 3.1-1.9-4.3Z"/></svg>
          </a>
          <a class="icon-btn" href="<?= htmlspecialchars($whatsapp) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 2.1A9.9 9.9 0 0 0 2.1 12c0 1.7.4 3.4 1.3 4.9L2 22l5.2-1.4A9.9 9.9 0 1 0 12 2.1Zm0 18.1c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.1.8.8-3-.2-.3A8.1 8.1 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.8 1c-.1.1-.3.2-.5.1a6.6 6.6 0 0 1-3.2-2.8c-.1-.2 0-.3.1-.5l.7-.8c.1-.1.1-.3.1-.4 0-.1 0-.3-.1-.4l-.8-1.8c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.3c.1.2 1.6 2.5 3.9 3.4 2.3.9 2.3.6 2.7.6.4 0 1.3-.5 1.5-1 .2-.5.2-.9.1-1 0-.1-.2-.2-.4-.3Z"/></svg>
          </a>
        </div>

        <a class="btn btn--primary header__cta" href="<?= htmlspecialchars($BASE) ?>index.php#booking"><span class="header__cta-full">Записаться</span><span class="header__cta-short">Запись</span></a>

        <button class="burger" type="button" data-burger aria-expanded="false" aria-controls="mobile-menu" aria-label="Открыть меню">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>

    <div class="mobile-menu" id="mobile-menu" data-mobile-menu hidden>
      <a class="mobile-menu__phone" href="tel:<?= htmlspecialchars($phone_href) ?>">
        <span><?= htmlspecialchars($phone) ?></span>
        <span class="btn btn--call btn--sm">Позвонить</span>
      </a>
      <nav aria-label="Мобильная навигация">
        <details class="mobile-drop">
          <summary>Услуги</summary>
          <div class="mobile-drop__list">
            <?php foreach ($clinic['services'] as $slug => $item): ?>
              <a href="<?= htmlspecialchars(zubr_service_url($slug, $BASE)) ?>"><?= htmlspecialchars($item['name']) ?></a>
            <?php endforeach; ?>
            <a href="<?= htmlspecialchars($BASE) ?>services/">Все услуги</a>
          </div>
        </details>
        <a href="<?= htmlspecialchars($BASE) ?>about.php">О клинике</a>
        <a href="<?= htmlspecialchars($BASE) ?>index.php#doctors">Врачи</a>
        <a href="<?= htmlspecialchars($BASE) ?>prices.php">Цены</a>
        <a href="<?= htmlspecialchars($BASE) ?>index.php#reviews">Отзывы</a>
        <a href="<?= htmlspecialchars($BASE) ?>faq.php">Вопросы</a>
        <a href="<?= htmlspecialchars($BASE) ?>index.php#contacts">Контакты</a>
        <a href="<?= htmlspecialchars($BASE) ?>index.php#map">Карта зубов</a>
        <a class="btn btn--primary" href="<?= htmlspecialchars($BASE) ?>index.php#booking">Записаться на приём</a>
      </nav>
    </div>
  </header>
