<?php
// app/process_register.php
include __DIR__ . '/../config/config.php';
session_start();

// disini misal kita mau daftar akun baru
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../public/register.php');
  exit;
}

// disini adalah data2 yang kita masukkan pada saat kita membuat akun register atau daftar akun
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// disini semisal salah satu pada saat kita daftar data nya itu kosong
// maka nanti keluar alert atau message yaitu semua field wajib diisi
if ($name === '' || $email === '' || $password === '') {
  header('Location: ../public/register.php?error=' . urlencode('Semua field wajib diisi'));
  exit;
}

// untuk memastikan suatu email itu memiliki struktur email yang valid
// nama@string.string nama@gmail.com nama@yahoo.com
// jika kita input email yang tidak sesuai atau tidak valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: ../public/register.php?error=' . urlencode('Email tidak valid'));
  exit;
}

// kalau misal kita masukkin password lebih kecil dari 6 karakter
// kita enggak boleh isi karakter kurang dari 6 karakter
if (strlen($password) < 6) {
  header('Location: ../public/register.php?error=' . urlencode('Password minimal 6 karakter'));
  exit;
}

// melakukan validasi apakah email tersebut terdaftar gtu
// kita daftar email itu harus unique atau email enggak boleh sama
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
  mysqli_stmt_close($stmt);
  header('Location: ../public/register.php?error=' . urlencode('Email sudah terdaftar'));
  exit;
}
mysqli_stmt_close($stmt);

// simpan user
// password akan otomatis di hash jika user sudah membuat akun
$password_hash = password_hash($password, PASSWORD_DEFAULT);
// akun tersebut akan disimpan di dalam database dengan role default yaitu user
$stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'user')");
if (!$stmt) {
  header('Location: ../public/register.php?error=' . urlencode('Terjadi kesalahan server'));
  exit;
}
mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password_hash);
// ok berarti akun tersebut kita masukin ya atau eksekusi
$ok = mysqli_stmt_execute($stmt);
// pemrosesan akun
mysqli_stmt_close($stmt);
// tutup koneksi
mysqli_close($conn);

if ($ok) {
  // kalau misal dia itu berhasil di daftar
  header('Location: ../public/login.php?registered=1');
  exit;
} else {
  // enggak berhasil daftar akun
  header('Location: ../public/register.php?error=' . urlencode('Gagal membuat akun'));
  exit;
}
