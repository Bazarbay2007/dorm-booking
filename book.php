<?php
require __DIR__ . '/config.php';
require_login();
$title = 'Брондау';
$st = $pdo->prepare('SELECT * FROM rooms WHERE id=?');
$st->execute([(int)($_GET['room_id'] ?? 0)]);
$room = $st->fetch();
if (!$room) die('Бөлме табылмады.');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // CREATE
    if (!csrf_ok()) die('CSRF қатесі');
    $in = $_POST['check_in'] ?? '';
    $out = $_POST['check_out'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $errors = validate_dates($in, $out);
    if (mb_strlen($note) > 255) $errors[] = 'Ескертпе тым ұзын.';
    if (!$errors && !room_is_free($pdo, $room['id'], $in, $out)) $errors[] = 'Бұл күндерге бөлме бос емес.';
    if (!$errors) {
        $st = $pdo->prepare('INSERT INTO bookings (user_id,room_id,check_in,check_out,note) VALUES (?,?,?,?,?)');
        $st->execute([$_SESSION['user']['id'], $room['id'], $in, $out, $note]);
        header('Location: ' . BASE . '/my_bookings.php?done=1');
        exit;
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="box">
<h2>Бөлме №<?= e($room['room_number']) ?> брондау</h2>
<?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post">
  <?= csrf_field() ?>
  <label>Кіру күні</label><input type="date" name="check_in" value="<?= e($_POST['check_in'] ?? '') ?>" required>
  <label>Шығу күні</label><input type="date" name="check_out" value="<?= e($_POST['check_out'] ?? '') ?>" required>
  <label>Ескертпе</label><textarea name="note" rows="3"><?= e($_POST['note'] ?? '') ?></textarea>
  <button class="btn">Брондау</button>
  <a class="btn gray" href="<?= BASE ?>/index.php">Артқа</a>
</form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
