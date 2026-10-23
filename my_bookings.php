<?php
require __DIR__ . '/config.php';
require_login();
$title = 'Менің брондарым';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // DELETE
    if (!csrf_ok()) die('CSRF қатесі');
    $st = $pdo->prepare('DELETE FROM bookings WHERE id=? AND user_id=?');
    $st->execute([(int)$_POST['id'], $_SESSION['user']['id']]);
    header('Location: ' . BASE . '/my_bookings.php?deleted=1');
    exit;
}
$st = $pdo->prepare('SELECT b.*, r.room_number, r.price FROM bookings b JOIN rooms r ON r.id=b.room_id WHERE b.user_id=? ORDER BY b.check_in DESC');  // READ
$st->execute([$_SESSION['user']['id']]);
$rows = $st->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h1>Менің брондарым</h1>
<?php if (isset($_GET['done'])): ?><div class="ok">Брондау сәтті жасалды!</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="ok">Брондау жойылды.</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="ok">Өзгерістер сақталды.</div><?php endif; ?>
<?php if (!$rows): ?><p>Брондарыңыз жоқ.</p><?php else: ?>
<div style="overflow-x:auto"><table>
<tr><th>Бөлме</th><th>Кіру</th><th>Шығу</th><th>Ескертпе</th><th></th></tr>
<?php foreach ($rows as $b): ?>
<tr>
  <td>№<?= e($b['room_number']) ?></td>
  <td><?= e($b['check_in']) ?></td>
  <td><?= e($b['check_out']) ?></td>
  <td><?= e($b['note']) ?></td>
  <td>
    <a class="btn" href="<?= BASE ?>/edit_booking.php?id=<?= (int)$b['id'] ?>">Өзгерту</a>
    <form method="post" style="display:inline" onsubmit="return confirm('Жоюға сенімдісіз бе?')">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
      <button class="btn red">Жою</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table></div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
