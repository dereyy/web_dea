<?php
// app/process_comment.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

// hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
  exit;
}

// harus login untuk kirim komentar
if (empty($_SESSION['user_id'])) {
  header('Location: /public/login.php');
  exit;
}

// CSRF check
if (!verify_csrf()) {
  http_response_code(400);
  echo "Invalid CSRF token";
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? 'create';

if ($action === 'delete') {
  $delete_id = (int)($_POST['delete_id'] ?? 0);
  if ($delete_id > 0) {
    // cek pemilik komentar
    $stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($res && (int)$res['user_id'] === $user_id) {
      $del = $conn->prepare("DELETE FROM comments WHERE id = ?");
      $del->bind_param("i", $delete_id);
      $del->execute();
      $del->close();
    }
  }
  header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
  exit;
}

// create
$article_id = (int)($_POST['article_id'] ?? 0);
$body = trim($_POST['body'] ?? '');

if ($article_id <= 0 || $body === '') {
  header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
  exit;
}

// limit panjang
if (mb_strlen($body) > 2000) $body = mb_substr($body, 0, 2000);

// pastikan artikel ada
$chk = $conn->prepare("SELECT id FROM articles WHERE id = ? LIMIT 1");
$chk->bind_param("i", $article_id);
$chk->execute();
$r = $chk->get_result()->fetch_assoc();
$chk->close();
if (!$r) {
  header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
  exit;
}

// insert
$ins = $conn->prepare("INSERT INTO comments (article_id, user_id, body) VALUES (?, ?, ?)");
$ins->bind_param("iis", $article_id, $user_id, $body);
$ins->execute();
$ins->close();

// redirect balik ke article (agar komentar terlihat). akan kembali ke referer
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? "/public/article.php?slug=" . urlencode($_POST['slug'] ?? '')));
exit;
