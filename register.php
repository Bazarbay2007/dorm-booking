<?php
require __DIR__ . '/config.php';
$title = 'Тіркелу';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) die('CSRF қатесі');
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if (mb_strlen($name) < 2) $errors[] = 'Аты-жөні кемінде 2 таңба болуы керек.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email дұрыс емес.';
    if (strlen($pass) < 6) $errors[] = 'Құпиясөз кемінде 6 таңба болуы керек.';
    if ($pass !== $pass2) $errors[] = 'Құпиясөздер сәйкес емес.';
    if (!$errors) {
        $st = $pdo->prepare('SELECT id FROM users WHERE email=?');
        $st->execute([$email]);
        if ($st->fetch()) $errors[] = 'Бұл email бұрын тіркелген.';
    }
    if (!$errors) {
        $st = $pdo->prepare('INSERT INTO users (full_name,email,password_hash) VALUES (?,?,?)');
        $st->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        header('Location: ' . BASE . '/login.php?registered=1');
        exit;
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="box">
<h2>Тіркелу</h2>
<?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post">
  <?= csrf_field() ?>
  <label>Аты-жөні</label><input name="full_name" value="<?= e($_POST['full_name'] ?? '') ?>" required>
  <label>Email</label><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
  <label>Құпиясөз</label><input type="password" name="password" required>
  <label>Құпиясөзді қайталаңыз</label><input type="password" name="password2" required>
  <button class="btn">Тіркелу</button>
</form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
