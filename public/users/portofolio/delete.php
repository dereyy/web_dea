<?php
// public/users/portofolio/delete.php
require_once __DIR__ . '/../../../app/auth.php';
require_login();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../app/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!verify_csrf()) {
    // simple failure - redirect back
    header('Location: index.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

$uid = current_user_id();

// ambil featured image pastikan milik user
$stmt = mysqli_prepare($conn, "SELECT featured_image FROM articles WHERE id = ? AND author_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $img);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// hapus row
$stmt = mysqli_prepare($conn, "DELETE FROM articles WHERE id = ? AND author_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok && $img) {
    $path = __DIR__ . '/../../../public/' . $img;
    if (file_exists($path)) @unlink($path);
}

header('Location: index.php');
exit;
