<?php
// app/seed_users.php
// Jalankan sekali untuk membuat 2 akun: admin@example.com / user@example.com
// Hapus file ini setelah sukses untuk keamanan.

include __DIR__ . '/../config/config.php'; // pastikan path benar

$users = [
  ['name' => 'Admin Kampus', 'email' => 'admin@example.com', 'password' => 'admin123', 'role' => 'admin'],
  ['name' => 'User Biasa',   'email' => 'user@example.com',  'password' => 'user12345', 'role' => 'user'],
];

foreach ($users as $u) {
  // Cek apakah email sudah ada
  $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
  mysqli_stmt_bind_param($stmt, "s", $u['email']);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);
  if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "User {$u['email']} sudah ada. <br>";
    mysqli_stmt_close($stmt);
    continue;
  }
  mysqli_stmt_close($stmt);

  $hash = password_hash($u['password'], PASSWORD_DEFAULT);
  $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "ssss", $u['name'], $u['email'], $hash, $u['role']);
  $ok = mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  echo $ok ? "Inserted {$u['email']} ({$u['role']})<br>" : "Gagal insert {$u['email']}<br>";
}

mysqli_close($conn);