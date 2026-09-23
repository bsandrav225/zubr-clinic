<?php
$BASE = '';
require_once __DIR__ . '/includes/boot.php';

$pageTitle = 'Вопросы о клинике «Зубр» — запись, рассрочка, гарантия, лечение во сне';
$pageDescription = 'Ответы на частые вопросы: как проходит первый визит, есть ли рассрочка и гарантия, можно ли лечить зубы во сне и принимают ли детей.';
$pageCanonical = SITE_URL . '/faq.php';
$activeNav = 'faq';

$ldFaq = [
  '@context' => 'https://schema.org',
  '@type' => 'FAQPage',
  'mainEntity' => array_map(static function ($item) {
      return [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
      ];
  }, $clinic['faq']),
];
$extraHead = '<script type="application/ld+json">' . json_encode($ldFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';

require __DIR__ . '/includes/header.php';
?>
  <main id="main">
    <section class="page-hero">
      <div class="container">
        <p class="page-hero__crumb">
          <a href="index.php">Главная</a> · Вопросы
        </p>
        <h1>Частые вопросы</h1>
        <p class="page-hero__lead">Коротко о диагностике, записи, рассрочке и гарантии. Если не нашли ответ — позвоните или напишите в мессенджер.</p>
      </div>
    </section>

    <section class="container" style="padding-bottom:3rem">
      <div class="faq-list">
        <?php foreach ($clinic['faq'] as $item): ?>
          <details class="faq-item">
            <summary><?= htmlspecialchars($item['q']) ?></summary>
            <p><?= htmlspecialchars($item['a']) ?></p>
          </details>
        <?php endforeach; ?>
      </div>

      <div class="cta-banner" style="margin-top:2rem">
        <div>
          <h2>Остались вопросы?</h2>
          <p>Администратор подскажет по услуге и подберёт время. Или откройте карту зубов — так проще понять, что нужно лечить.</p>
        </div>
        <div class="hero__actions">
          <a class="btn btn--primary btn--lg" href="index.php#booking">Записаться</a>
          <a class="btn btn--ghost btn--lg" href="index.php#map" style="background:#fff">Карта зубов</a>
        </div>
      </div>
    </section>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
