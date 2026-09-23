<?php
declare(strict_types=1);
require __DIR__ . '/../api/bootstrap.php';
start_admin_session();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim((string)($_POST['username'] ?? ''));
    $pass = (string)($_POST['password'] ?? '');
    if (verify_admin($user, $pass)) {
        $_SESSION['admin_ok'] = true;
        $_SESSION['admin_user'] = $user;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Неверный логин или пароль';
}

if (!empty($_SESSION['admin_ok'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход — админ-панель Зубр</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="login-page">
  <form class="login-card" method="post" autocomplete="username">
    <p class="brand">Зубр</p>
    <h1>Админ-панель</h1>
    <p class="muted">Управление заявками, контентом и расписанием</p>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <label>
      <span>Логин</span>
      <input type="text" name="username" required value="admin">
    </label>
    <label>
      <span>Пароль</span>
      <input type="password" name="password" required>
    </label>
    <button type="submit">Войти</button>
  </form>
</body>
</html>