<?php
$teeth = $clinic['teeth'];
$props = $clinic['prosthetics_map'];
$quadName = [
    1 => 'верхний правый',
    2 => 'верхний левый',
    3 => 'нижний левый',
    4 => 'нижний правый',
];

function zubr_mouth_type(int $pos): array
{
    if ($pos <= 2) {
        return ['label' => 'резец', 'w' => 24, 'h' => 66];
    }
    if ($pos === 3) {
        return ['label' => 'клык', 'w' => 22, 'h' => 76];
    }
    if ($pos <= 5) {
        return ['label' => 'премоляр', 'w' => 28, 'h' => 58];
    }
    return ['label' => 'моляр', 'w' => 36, 'h' => 52];
}

function zubr_mouth_arch(bool $isUpper, array $teeth, array $quadName): string
{
    $order = [8, 7, 6, 5, 4, 3, 2, 1, 1, 2, 3, 4, 5, 6, 7, 8];
    $quadLeft = $isUpper ? 2 : 3;
    $quadRight = $isUpper ? 1 : 4;
    $infos = [];
    foreach ($order as $i => $pos) {
        $infos[] = ['pos' => $pos, 'quad' => $i < 8 ? $quadLeft : $quadRight] + zubr_mouth_type($pos);
    }
    $gap = 3;
    $totalW = 0;
    foreach ($infos as $t) {
        $totalW += $t['w'];
    }
    $totalW += $gap * (count($infos) - 1);
    $x = 340 - $totalW / 2;
    $gumY = $isUpper ? 140 : 480;
    $html = '';
    foreach ($infos as $i => $t) {
        $cx = $x + $t['w'] / 2;
        $edge = abs($i - 7.5) / 7.5;
        $h = $t['h'] - $edge * 10;
        $rectY = $isUpper ? $gumY : $gumY - $h;
        $fdi = (string)($t['quad'] * 10 + $t['pos']);
        $meta = $teeth[$fdi] ?? ['name' => $quadName[$t['quad']] . ' ' . $t['label'], 'hint' => '', 'group' => $t['label']];
        $html .= '<g class="mouth-tooth" data-kind="tooth" data-id="' . htmlspecialchars($fdi) . '"'
            . ' tabindex="0" role="button" aria-label="' . htmlspecialchars($meta['name']) . '">'
            . '<rect x="' . number_format($cx - $t['w'] / 2, 1, '.', '') . '" y="' . number_format($rectY, 1, '.', '') . '"'
            . ' width="' . $t['w'] . '" height="' . number_format($h, 1, '.', '') . '" rx="6"/>'
            . '</g>';
        $x += $t['w'] + $gap;
    }
    return $html;
}
?>
<section class="section dentamap" id="map">
  <div class="container">
    <div class="section__head section__head--wide">
      <p class="eyebrow">Навигация по улыбке</p>
      <h2 class="section__title">Нажмите на зуб — появится название</h2>
      <p class="section__lead">Схема зубного ряда: верхняя и нижняя челюсти, 32 зуба. Клик открывает тип зуба и связанные процедуры.</p>
    </div>

    <div class="mouth-map" data-jaw>
      <h3 class="sr-only">Интерактивная схема зубного ряда: верхняя и нижняя челюсти, каждый зуб можно выбрать</h3>
      <svg id="mouthSvg" class="mouth-map__svg" viewBox="0 0 680 620" role="img" aria-label="Схема зубного ряда">
        <title>Схема зубного ряда</title>
        <desc>Верхняя и нижняя челюсти с зубами. Каждый зуб можно выбрать, чтобы узнать о лечении.</desc>
        <ellipse cx="340" cy="310" rx="275" ry="235" fill="#163a54"/>
        <ellipse cx="340" cy="310" rx="232" ry="195" fill="#1e6a8c"/>
        <ellipse cx="340" cy="400" rx="110" ry="55" fill="#3d8eb0"/>
        <?= zubr_mouth_arch(true, $teeth, $quadName) ?>
        <?= zubr_mouth_arch(false, $teeth, $quadName) ?>
      </svg>

      <div class="tooth-info" data-tooth-info>
        <div>
          <p class="tooth-info__title" data-map-title>Выберите зуб на схеме</p>
          <p class="tooth-info__sub" data-map-text>Нажмите на зуб, чтобы узнать о лечении</p>
          <div class="tooth-info__links" data-map-links></div>
        </div>
        <a class="btn btn--primary btn--sm" data-map-book hidden href="<?= htmlspecialchars($BASE) ?>index.php#booking">Записаться ↗</a>
      </div>
    </div>
  </div>
</section>

<section class="section prosth-guide" id="prosthetics">
  <div class="container">
    <div class="section__head section__head--wide">
      <p class="eyebrow">Протезирование</p>
      <h2 class="section__title">Какая конструкция для какого случая</h2>
      <p class="section__lead">Что это такое и когда врач предлагает именно этот вариант.</p>
    </div>
    <div class="prosth-grid">
      <?php foreach ($props as $id => $p): ?>
        <article class="prosth-card">
          <?php if (!empty($p['image'])): ?>
            <div class="prosth-card__art">
              <img src="<?= htmlspecialchars($BASE . $p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" width="640" height="400" loading="lazy">
            </div>
          <?php endif; ?>
          <div class="prosth-card__body">
            <p class="prosth-card__type"><?= htmlspecialchars($p['subtitle']) ?></p>
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p><?= htmlspecialchars($p['text']) ?></p>
            <p class="prosth-card__when"><strong>Когда ставят.</strong> <?= htmlspecialchars($p['when'] ?? '') ?></p>
            <div class="prosth-card__foot">
              <span><?= htmlspecialchars($p['price']) ?></span>
              <a href="<?= htmlspecialchars(zubr_service_url($p['service'], $BASE)) ?>">Подробнее</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script type="application/json" id="map-data"><?= json_encode([
    'teeth' => $clinic['teeth'],
    'services' => $clinic['services'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
