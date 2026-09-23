<?php
$BASE = '../';
require_once __DIR__ . '/../includes/boot.php';

$serviceName = trim((string)($_GET['service'] ?? ''));
if ($serviceName === '') {
    header('Location: ./');
    exit;
}
$service = zubr_find_service($serviceName);
if (!$service) {
    http_response_code(404);
    $pageTitle = 'Услуга не найдена | Клиника Зубр';
    $pageDescription = 'Такой услуги нет в каталоге клиники «Зубр».';
    $pageRobots = 'noindex, follow';
    $pageCanonical = SITE_URL . '/services/';
    require dirname(__DIR__) . '/includes/header.php';
    echo '<main id="main"><section class="page-hero"><div class="container">';
    echo '<h1>Услуга не найдена</h1><p class="page-hero__lead">Вернитесь в каталог или на главную.</p>';
    echo '<div class="hero__actions"><a class="btn btn--primary" href="../services/">Все услуги</a>';
    echo '<a class="btn btn--ghost" href="../index.php">На главную</a></div></div></section></main>';
    require dirname(__DIR__) . '/includes/footer.php';
    exit;
}

$slug = $service['slug'];
$canonicalUrl = SITE_URL . '/services/service_template.php?service=' . rawurlencode($service['name']);
$pageTitle = $service['name'] . ' — цена, описание и запись | Клиника Зубр';
$pageDescription = strip_tags($service['hero_lead']) . ' Цена ' . $service['price'] . ', длительность ' . $service['duration'] . '.';
$pageCanonical = $canonicalUrl;
$activeNav = 'services';
$selectedService = $service['name'];

$ldService = [
  '@context' => 'https://schema.org',
  '@type' => 'Service',
  'serviceType' => $service['name'],
  'name' => $service['name'],
  'description' => strip_tags($service['hero_lead']),
  'url' => $canonicalUrl,
  'provider' => [
    '@type' => 'Dentist',
    'name' => SITE_NAME,
    'telephone' => $phone,
    'url' => SITE_URL . '/',
  ],
  'offers' => [
    '@type' => 'Offer',
    'priceCurrency' => 'RUB',
    'price' => preg_replace('/\D+/', '', $service['price']) ?: '0',
    'url' => $canonicalUrl,
  ],
];
$ldBreadcrumb = [
  '@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => SITE_URL . '/'],
    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Услуги', 'item' => SITE_URL . '/services/'],
    ['@type' => 'ListItem', 'position' => 3, 'name' => $service['name'], 'item' => $canonicalUrl],
  ],
];
$extraHead = '<script type="application/ld+json">' . json_encode($ldService, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>'
    . '<script type="application/ld+json">' . json_encode($ldBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';

require dirname(__DIR__) . '/includes/header.php';
?>
  <main id="main">
    <section class="page-hero">
      <div class="container">
        <p class="page-hero__crumb">
          <a href="../index.php">Главная</a> ·
          <a href="./">Услуги</a> ·
          <?= htmlspecialchars($service['name']) ?>
        </p>
        <h1><?= htmlspecialchars($service['name']) ?></h1>
        <p class="page-hero__lead"><?= htmlspecialchars($service['hero_lead']) ?></p>
        <div class="page-meta">
          <span class="page-meta__item">Цена: <?= htmlspecialchars($service['price']) ?></span>
          <span class="page-meta__item">Длительность: <?= htmlspecialchars($service['duration']) ?></span>
        </div>
      </div>
    </section>

    <section class="container service-layout">
      <div class="service-main">
        <article class="service-card">
          <h2>О процедуре</h2>
          <p><?= nl2br(htmlspecialchars($service['description']), false) ?></p>
        </article>

        <article class="service-card">
          <h2>Что входит в услугу</h2>
          <ul class="service-list">
            <?php foreach ($service['includes'] as $item): ?>
              <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </article>

        <article class="service-card">
          <h2>Показания</h2>
          <ul class="service-list">
            <?php foreach ($service['indications'] as $item): ?>
              <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </article>

        <article class="service-card">
          <h2>Противопоказания</h2>
          <ul class="service-list">
            <?php foreach ($service['contraindications'] as $item): ?>
              <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      </div>

      <aside class="service-aside">
        <div class="service-cta">
          <h2>Записаться на приём</h2>
          <p>Выберите удобную дату на главной — услуга «<?= htmlspecialchars($service['name']) ?>» будет уже выбрана.</p>
          <a class="btn btn--primary btn--lg" href="../index.php?service=<?= rawurlencode($service['name']) ?>#booking">
            Записаться
          </a>
        </div>

        <div class="service-card">
          <h2>Другие услуги</h2>
          <ul class="service-list">
            <?php foreach ($clinic['services'] as $otherSlug => $s): ?>
              <?php if ($otherSlug === $slug) continue; ?>
              <li>
                <a href="service_template.php?service=<?= rawurlencode($s['name']) ?>">
                  <?= htmlspecialchars($s['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </aside>
    </section>
  </main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
