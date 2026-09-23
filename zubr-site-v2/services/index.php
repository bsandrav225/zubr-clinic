<?php
$BASE = '../';
require_once __DIR__ . '/../includes/boot.php';

$pageTitle = 'Услуги стоматологии «Зубр» — лечение, имплантация, протезирование';
$pageDescription = 'Полный каталог услуг клиники «Зубр»: терапия, хирургия, имплантация, ортопедия, гигиена, отбеливание, детский приём, КТ и лечение во сне.';
$pageCanonical = SITE_URL . '/services/';
$activeNav = 'services';

require dirname(__DIR__) . '/includes/header.php';
?>
  <main id="main">
    <section class="page-hero">
      <div class="container">
        <p class="page-hero__crumb">
          <a href="../index.php">Главная</a> · Услуги
        </p>
        <h1>Услуги клиники</h1>
        <p class="page-hero__lead">Все направления в одном месте: от гигиены и лечения кариеса до имплантации и протезов из собственной лаборатории.</p>
        <div class="hero__actions">
          <a class="btn btn--primary" href="../index.php#map">Карта зубов и протезов</a>
          <a class="btn btn--ghost" href="../prices.php">Прайс</a>
        </div>
      </div>
    </section>

    <section class="container" style="padding-bottom:3.5rem">
      <?php foreach ($clinic['categories'] as $cat): ?>
        <div class="section__head" style="margin-top:1.5rem">
          <p class="eyebrow"><?= htmlspecialchars($cat['lead']) ?></p>
          <h2 class="section__title"><?= htmlspecialchars($cat['title']) ?></h2>
        </div>
        <div class="catalog-grid">
          <?php foreach ($cat['items'] as $slug): ?>
            <?php $item = $clinic['services'][$slug] ?? null; if (!$item) continue; ?>
            <a class="catalog-card" href="<?= htmlspecialchars(zubr_service_url($slug, $BASE)) ?>">
              <h3><?= htmlspecialchars($item['name']) ?></h3>
              <p><?= htmlspecialchars($item['short']) ?></p>
              <span class="catalog-card__foot">
                <?= htmlspecialchars($item['price']) ?>
                <span aria-hidden="true">→</span>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </section>
  </main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
