<?php
// app/process_create_user.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/config.php';

require_admin();

// hanya POST
// Halaman UI Admin
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}

// CSRF
$csrf = $_POST['csrf'] ?? '';
if (empty($csrf) || !isset($_SESSION['csrf_add_user']) || !hash_equals($_SESSION['csrf_add_user'], $csrf)) {
    $_SESSION['flash_error'] = 'Form tidak valid (CSRF).';
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}
unset($_SESSION['csrf_add_user']);

// ambil & sanitasi input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

$allowed_roles = ['admin', 'editor', 'user'];

// validasi kalau misal salah satu data ada yang kosong
if ($name === '' || $email === '' || $password === '' || $role === '') {
    $_SESSION['flash_error'] = 'Semua field wajib diisi.';
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}
// melakukan validasi email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = 'Email tidak valid.';
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}
// dia kalau milih role lain yg tidak ada
if (!in_array($role, $allowed_roles)) {
    $_SESSION['flash_error'] = 'Role tidak valid.';
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}
// password itu minimal 6
if (strlen($password) < 6) {
    $_SESSION['flash_error'] = 'Password minimal 6 karakter.';
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}

// untuk memastikan tidak ada email yang sama
// cek unique email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $_SESSION['flash_error'] = 'Email sudah terdaftar.';
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}
$stmt->close();

// hash password
// setiap kita buat akun baru atau tambah akun baru dia akan otomatis
// di has password nya
$hash = password_hash($password, PASSWORD_DEFAULT);

// insert user
// password123
// $2y$10$u6gvptM
$stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())");
if (!$stmt) {
    $_SESSION['flash_error'] = 'Terjadi kesalahan server.';
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}
// sebelum tambah data dia perlu parameter
$stmt->bind_param('ssss', $name, $email, $hash, $role);
// buat commit atau jalanin
$ok = $stmt->execute();
$stmt->close();

// kalau berhasil dia ke kondisi pertama
if ($ok) {
    $_SESSION['flash'] = 'User berhasil dibuat.';
    header('Location: /pert6-web-blog/public/admin/role_management.php');
    exit;
// kalau dia gagal dia akan ke kondisi lainnya atau terakhir
} else {
    $_SESSION['flash_error'] = 'Gagal menyimpan user: ' . $conn->error;
    header('Location: /pert6-web-blog/public/admin/add_user.php');
    exit;
}
