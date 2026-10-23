<?php
require __DIR__ . '/config.php';
require_login();
$title = 'Брондауды өзгерту';
$st = $pdo->prepare('SELECT * FROM bookings WHERE id=? AND user_id=?');
$st->execute([(int)($_GET['id'] ?? 0), $_SESSION['user']['id']]);
$b = $st->fetch();
if (!$b) die('Брондау табылмады.');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // UPDATE
    if (!csrf_ok()) die('CSRF қатесі');
    $in = $_POST['check_in'] ?? '';
    $out = $_POST['check_out'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $errors = validate_dates($in, $out);
    if (!$errors && !room_is_free($pdo, $b['room_id'], $in, $out, $b['id'])) $errors[] = 'Бұл күндерге бөлме бос емес.';
    if (!$errors) {
        $st = $pdo->prepare('UPDATE bookings SET check_in=?, check_out=?, note=? WHERE id=? AND user_id=?');
        $st->execute([$in, $out, $note, $b['id'], $_SESSION['user']['id']]);
        header('Location: ' . BASE . '/my_bookings.php?updated=1');
        exit;
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="box">
<h2>Брондауды өзгерту</h2>
<?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post">
  <?= csrf_field() ?>
  <label>Кіру күні</label><input type="date" name="check_in" value="<?= e($_POST['check_in'] ?? $b['check_in']) ?>" required>
  <label>Шығу күні</label><input type="date" name="check_out" value="<?= e($_POST['check_out'] ?? $b['check_out']) ?>" required>
  <label>Ескертпе</label><textarea name="note" rows="3"><?= e($_POST['note'] ?? $b['note']) ?></textarea>
  <button class="btn">Сақтау</button>
  <a class="btn gray" href="<?= BASE ?>/my_bookings.php">Болдырмау</a>
</form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
