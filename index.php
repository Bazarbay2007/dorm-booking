<?php
require __DIR__ . '/config.php';
$title = 'Бөлмелер';

// Іздеу, сүзу, сұрыптау (серверде)
$q     = trim($_GET['q'] ?? '');
$floor = (int)($_GET['floor'] ?? 0);
$sorts = ['price_asc' => 'price ASC', 'price_desc' => 'price DESC', 'num' => 'room_number ASC'];
$sort  = $_GET['sort'] ?? 'num';
if (!isset($sorts[$sort])) $sort = 'num';

$where = ' WHERE 1=1';
$params = [];
if ($q !== '')   { $where .= ' AND (room_number LIKE ? OR description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($floor > 0)  { $where .= ' AND floor = ?'; $params[] = $floor; }

// Пагинация
$per = 6;
$page = max(1, (int)($_GET['page'] ?? 1));
$st = $pdo->prepare('SELECT COUNT(*) FROM rooms' . $where);
$st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
$offset = ($page - 1) * $per;

$st = $pdo->prepare('SELECT * FROM rooms' . $where . ' ORDER BY ' . $sorts[$sort] . ' LIMIT ' . $per . ' OFFSET ' . $offset);
$st->execute($params);
$rooms = $st->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<h1>Жатақхана бөлмелері</h1>
<form class="filters" method="get">
  <input type="text" name="q" placeholder="Іздеу (нөмір, сипаттама)" value="<?= e($q) ?>">
  <select name="floor">
    <option value="0">Барлық қабат</option>
    <?php for ($i = 1; $i <= 5; $i++): ?>
      <option value="<?= $i ?>" <?= $floor == $i ? 'selected' : '' ?>><?= $i ?>-қабат</option>
    <?php endfor; ?>
  </select>
  <select name="sort">
    <option value="num" <?= $sort=='num'?'selected':'' ?>>Нөмір бойынша</option>
    <option value="price_asc" <?= $sort=='price_asc'?'selected':'' ?>>Бағасы: арзаннан</option>
    <option value="price_desc" <?= $sort=='price_desc'?'selected':'' ?>>Бағасы: қымбаттан</option>
  </select>
  <button class="btn">Іздеу</button>
</form>

<?php if (!$rooms): ?><p>Бөлме табылмады.</p><?php endif; ?>
<div class="grid">
<?php foreach ($rooms as $r): ?>
  <div class="card">
    <h3>Бөлме №<?= e($r['room_number']) ?></h3>
    <p><?= e($r['floor']) ?>-қабат · <?= e($r['capacity']) ?> орын</p>
    <p><?= e($r['description']) ?></p>
    <p><b><?= number_format($r['price'], 0, '', ' ') ?> ₸ / ай</b></p>
    <a class="btn" href="<?= BASE ?>/book.php?room_id=<?= (int)$r['id'] ?>">Брондау</a>
  </div>
<?php endforeach; ?>
</div>

<div class="pager">
<?php for ($i = 1; $i <= $pages; $i++):
  $link = http_build_query(['q' => $q, 'floor' => $floor, 'sort' => $sort, 'page' => $i]); ?>
  <a class="<?= $i == $page ? 'cur' : '' ?>" href="?<?= e($link) ?>"><?= $i ?></a>
<?php endfor; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
