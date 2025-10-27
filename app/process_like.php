<?php
// app/process_like.php
if (session_status() === PHP_SESSION_NONE) session_start();

// includes: sesuaikan path bila config/db ada di lokasi lain
require_once __DIR__ . '/..' . '/config/config.php'; // <-- pastikan file config di folder config/
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

// hanya POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
  exit;
}

// cek login
if (empty($_SESSION['user_id'])) {
  header('Location: /public/login.php');
  exit;
}

// CSRF check (fungsi verify_csrf() ada di functions.php)
if (!verify_csrf()) {
  http_response_code(400);
  echo "Invalid CSRF token";
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$article_id = (int)($_POST['article_id'] ?? 0);

if ($article_id <= 0) {
  header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
  exit;
}

// optional: cek artikel ada (boleh dilewati, tapi direkomendasikan)
$stmtChk = $conn->prepare("SELECT id FROM articles WHERE id = ? LIMIT 1");
$stmtChk->bind_param("i", $article_id);
$stmtChk->execute();
$resChk = $stmtChk->get_result();
if ($resChk->num_rows === 0) {
  $stmtChk->close();
  header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
  exit;
}
$stmtChk->close();

// toggle like
$stmt = $conn->prepare("SELECT id FROM likes WHERE article_id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $article_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
  // unlike
  $row = $res->fetch_assoc();
  $stmt->close();

  $del = $conn->prepare("DELETE FROM likes WHERE id = ?");
  $del->bind_param("i", $row['id']);
  $del->execute();
  $del->close();
} else {
  // like
  $stmt->close();
  $ins = $conn->prepare("INSERT INTO likes (article_id, user_id) VALUES (?, ?)");
  $ins->bind_param("ii", $article_id, $user_id);
  $ins->execute();
  $ins->close();
}

// Kembalikan ke halaman asal
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;
