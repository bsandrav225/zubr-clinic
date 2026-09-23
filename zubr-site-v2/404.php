<?php
$BASE = '';
require_once __DIR__ . '/includes/boot.php';
http_response_code(404);

$pageTitle = 'Страница не найдена (404) | Клиника Зубр';
$pageDescription = 'Страница не найдена. Вернитесь на главную клиники «Зубр» или откройте каталог услуг.';
$pageRobots = 'noindex, follow';
$pageCanonical = SITE_URL . '/404.php';

require __DIR__ . '/includes/header.php';
?>
  <main id="main">
    <section class="page-hero">
      <div class="container" style="text-align:center; padding-top: 3rem; padding-bottom: 3rem;">
        <p class="page-hero__crumb"><a href="index.php">Главная</a> · 404</p>
        <h1>Страница не найдена</h1>
        <p class="page-hero__lead">Похоже, такой страницы больше нет или адрес введён неверно.</p>
        <div class="hero__actions" style="justify-content:center; margin-top: 1.5rem;">
          <a class="btn btn--primary btn--lg" href="index.php">На главную</a>
          <a class="btn btn--ghost btn--lg" href="services/">Смотреть услуги</a>
        </div>
        <p style="margin-top:1.5rem;">Или позвоните нам: <a href="tel:<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></p>
      </div>
    </section>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
