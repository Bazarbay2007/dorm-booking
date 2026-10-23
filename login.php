<?php
require __DIR__ . '/config.php';
$title = 'Кіру';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) die('CSRF қатесі');
    $st = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $st->execute([trim($_POST['email'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'full_name' => $u['full_name'], 'role' => $u['role']];
        header('Location: ' . BASE . '/index.php');
        exit;
    }
    $error = 'Email немесе құпиясөз қате.';
}
include __DIR__ . '/includes/header.php';
?>
<div class="box">
<h2>Кіру</h2>
<?php if (isset($_GET['registered'])): ?><div class="ok">Тіркелу сәтті! Енді кіріңіз.</div><?php endif; ?>
<?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
<form method="post">
  <?= csrf_field() ?>
  <label>Email</label><input type="email" name="email" required>
  <label>Құпиясөз</label><input type="password" name="password" required>
  <button class="btn">Кіру</button>
</form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
