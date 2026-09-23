<?php
$BASE = '';
require_once __DIR__ . '/includes/boot.php';

$pageTitle = 'Цены на стоматологию в клинике «Зубр»';
$pageDescription = 'Ориентировочные цены на лечение, гигиену, имплантацию, протезирование, отбеливание и детский приём. Итоговая стоимость — после диагностики и плана лечения.';
$pageCanonical = SITE_URL . '/prices.php';
$activeNav = 'prices';

require __DIR__ . '/includes/header.php';
?>
  <main id="main">
    <section class="page-hero">
      <div class="container">
        <p class="page-hero__crumb">
          <a href="index.php">Главная</a> · Цены
        </p>
        <h1>Цены</h1>
        <p class="page-hero__lead">Указаны стартовые стоимости «от». Точную сумму фиксируем в плане лечения после осмотра и, при необходимости, КТ. Доступна рассрочка.</p>
      </div>
    </section>

    <section class="container" style="padding-bottom:3rem">
      <?php foreach ($clinic['prices'] as $block): ?>
        <table class="price-table">
          <caption><?= htmlspecialchars($block['group']) ?></caption>
          <tbody>
            <?php foreach ($block['rows'] as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['price']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endforeach; ?>

      <div class="cta-banner">
        <div>
          <h2>Нужен расчёт под ваш случай?</h2>
          <p>Пришлите жалобу в форме записи — администратор подберёт время на консультацию и диагностику.</p>
        </div>
        <a class="btn btn--primary btn--lg" href="index.php#booking">Записаться</a>
      </div>
    </section>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
