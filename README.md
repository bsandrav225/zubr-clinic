# Клиника «Зубр»

Сайт стоматологии: страницы клиники, карта зубов, запись на приём, админка.

PHP 8, MySQL, обычный Apache (XAMPP или хостинг). GitHub Pages это не поднимет — нет PHP.

Админка: `admin` / `zubr2026` (смените, если выкладываете куда-то живьём).

## Запуск в XAMPP

1. Apache + MySQL.
2. Проект лежит в `C:\xampp\htdocs\zubr`.
3. В phpMyAdmin создайте базу `zubr_clinic` и импортируйте `zubr_clinic.sql`.
4. Сайт: http://localhost/zubr/
5. Админка: http://localhost/zubr/zubr-site-v2/admin/

`config/config.php` появится сам из примера. Для XAMPP обычно `root` и пустой пароль.

## Выложить на GitHub

```powershell
cd C:\xampp\htdocs\zubr
git init
git add .
git commit -m "Сайт клиники: запись, админка, MySQL"
git branch -M main
git remote add origin https://github.com/ВАШ_НИК/zubr-clinic.git
git push -u origin main
```

Репозиторий создайте заранее на github.com: Public, без README и .gitignore — они уже тут.

В About репозитория можно написать: PHP-сайт стоматологии, запись и админка.

Живое демо — только PHP-хостинг: залить `zubr-site-v2`, импортировать SQL, прописать базу в `config/config.php`.

## Папки

```
zubr_clinic.sql      дамп базы
index.php            редирект на сайт
zubr-site-v2/        сам сайт
```
