<?php
$BASE = '';
require_once __DIR__ . '/includes/boot.php';

$pageTitle = 'О клинике «Зубр» — диагностика, лаборатория, лечение под микроскопом';
$pageDescription = 'Стоматологическая клиника «Зубр» работает с 2020 года: своя зуботехническая лаборатория, КТ, лечение под микроскопом, лечение во сне, детский приём, рассрочка и гарантия.';
$pageCanonical = SITE_URL . '/about.php';
$activeNav = 'about';

require __DIR__ . '/includes/header.php';
?>
  <main id="main">
    <section class="page-hero">
      <div class="container">
        <p class="page-hero__crumb">
          <a href="index.php">Главная</a> · О клинике
        </p>
        <h1>О клинике «Зубр»</h1>
        <p class="page-hero__lead"><?= htmlspecialchars($clinic['about_lead']) ?></p>
      </div>
    </section>

    <section class="container page-prose">
      <?php foreach ($clinic['about_body'] as $p): ?>
        <p><?= htmlspecialchars($p) ?></p>
      <?php endforeach; ?>
    </section>

    <section class="section" id="why">
      <div class="container">
        <div class="section__head">
          <p class="eyebrow">Почему Зубр</p>
          <h2 class="section__title">Всё необходимое для качественного лечения — в одной клинике</h2>
          <p class="section__lead">От точной диагностики до восстановления зубов — без поездок по городу.</p>
        </div>
        <div class="adv-grid">
          <?php foreach ($clinic['advantages'] as $i => $adv): ?>
            <article class="adv-card">
              <span class="adv-card__n"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
              <h3><?= htmlspecialchars($adv['title']) ?></h3>
              <p><?= htmlspecialchars($adv['text']) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section process">
      <div class="container">
        <div class="section__head">
          <p class="eyebrow">Подход</p>
          <h2 class="section__title">Комплексный путь лечения</h2>
          <p class="section__lead">Диагностика, план, лечение и ортопедия в собственной лаборатории.</p>
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

    <div class="container">
      <div class="cta-banner">
        <div>
          <h2><?= htmlspecialchars($clinic['closing']) ?></h2>
          <p>Запишитесь на консультацию: разберём снимки и предложим варианты с понятной стоимостью.</p>
        </div>
        <a class="btn btn--primary btn--lg" href="index.php#booking">Записаться</a>
      </div>
    </div>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
