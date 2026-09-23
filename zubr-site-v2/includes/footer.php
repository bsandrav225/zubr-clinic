  <footer class="footer">
    <div class="container footer__top">
      <div>
        <a class="logo logo--light" href="<?= htmlspecialchars($BASE) ?>index.php">
          <span class="logo__mark" aria-hidden="true"><?= zubr_logo_svg(30) ?></span>
          <span class="logo__word">Зубр</span>
        </a>
        <p class="footer__about"><?= htmlspecialchars($clinic['closing']) ?></p>
      </div>
      <div class="footer__cols">
        <div>
          <h3>Пациенту</h3>
          <a href="<?= htmlspecialchars($BASE) ?>about.php">О клинике</a>
          <a href="<?= htmlspecialchars($BASE) ?>services/">Услуги</a>
          <a href="<?= htmlspecialchars($BASE) ?>prices.php">Цены</a>
          <a href="<?= htmlspecialchars($BASE) ?>faq.php">Вопросы</a>
          <a href="<?= htmlspecialchars($BASE) ?>index.php#booking">Онлайн-запись</a>
        </div>
        <div>
          <h3>Направления</h3>
          <a href="<?= htmlspecialchars(zubr_service_url('treatment', $BASE)) ?>">Лечение зубов</a>
          <a href="<?= htmlspecialchars(zubr_service_url('implant', $BASE)) ?>">Имплантация</a>
          <a href="<?= htmlspecialchars(zubr_service_url('prosthetics', $BASE)) ?>">Протезирование</a>
          <a href="<?= htmlspecialchars(zubr_service_url('kids', $BASE)) ?>">Детский приём</a>
          <a href="<?= htmlspecialchars($BASE) ?>index.php#map">Карта зубов</a>
        </div>
        <div>
          <h3>Документы и реквизиты</h3>
          <a href="<?= htmlspecialchars($BASE) ?>privacy.html">Политика конфиденциальности</a>
          <a href="<?= htmlspecialchars($BASE) ?>consent.html">Согласие на обработку данных</a>
          <p>ИНН <?= htmlspecialchars($inn) ?></p>
          <p>Оплата: наличные, карта, рассрочка</p>
          <p><a href="tel:<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></p>
        </div>
      </div>
    </div>
    <div class="container footer__bottom">
      <p>© 2026 Клиника «Зубр»</p>
      <div class="messengers">
        <a class="icon-btn icon-btn--light" href="<?= htmlspecialchars($telegram) ?>" target="_blank" rel="noopener noreferrer" aria-label="Telegram">
          <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M21.5 4.3 3.7 11.1c-1.2.5-1.2 1.2-.2 1.5l4.6 1.4 1.8 5.5c.2.7.4.9 1 .9.6 0 .9-.3 1.2-.6l2.7-2.6 4.5 3.3c.8.4 1.4.2 1.6-.8L22.9 5.5c.3-1.2-.4-1.7-1.4-1.2ZM9.6 14.1l9.1-5.7c.5-.3.9-.1.5.2l-7.4 6.7-.3 3.1-1.9-4.3Z"/></svg>
        </a>
        <a class="icon-btn icon-btn--light" href="<?= htmlspecialchars($whatsapp) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
          <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M12 2.1A9.9 9.9 0 0 0 2.1 12c0 1.7.4 3.4 1.3 4.9L2 22l5.2-1.4A9.9 9.9 0 1 0 12 2.1Zm0 18.1c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.1.8.8-3-.2-.3A8.1 8.1 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.8 1c-.1.1-.3.2-.5.1a6.6 6.6 0 0 1-3.2-2.8c-.1-.2 0-.3.1-.5l.7-.8c.1-.1.1-.3.1-.4 0-.1 0-.3-.1-.4l-.8-1.8c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.3c.1.2 1.6 2.5 3.9 3.4 2.3.9 2.3.6 2.7.6.4 0 1.3-.5 1.5-1 .2-.5.2-.9.1-1 0-.1-.2-.2-.4-.3Z"/></svg>
        </a>
      </div>
    </div>
  </footer>

  <div class="mobile-bar">
    <a class="mobile-bar__btn" href="tel:<?= htmlspecialchars($phone_href) ?>">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 4h3l1.5 4-2 1.5a12 12 0 0 0 5.5 5.5L16 13.5l4 1.5v3A2 2 0 0 1 18 20 14 14 0 0 1 4 6a2 2 0 0 1 2.5-2Z"/></svg>
      Позвонить
    </a>
    <a class="mobile-bar__btn mobile-bar__btn--main" href="<?= htmlspecialchars($BASE) ?>index.php#booking">Записаться</a>
  </div>

  <script src="<?= htmlspecialchars($BASE) ?>js/main.js"></script>
  <script src="<?= htmlspecialchars($BASE) ?>js/ui.js"></script>
</body>
</html>
