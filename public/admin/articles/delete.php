<?php
// public/admin/articles/delete.php
require_once __DIR__ . '/../../../app/auth.php';
require_admin();
require_once __DIR__ . '/../../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
  header('Location: index.php');
  exit;
}

// ambil featured image untuk dihapus
$stmt = mysqli_prepare($conn, "SELECT featured_image FROM articles WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $img);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// hapus row
$stmt = mysqli_prepare($conn, "DELETE FROM articles WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// hapus file jika berhasil
if ($ok && $img) {
  $path = __DIR__ . '/../../../public/' . $img;
  if (file_exists($path)) @unlink($path);
}

header('Location: index.php');
exit;
