<?php
// app/process_login.php (versi ditingkatkan)
require_once __DIR__ . '/../config/config.php';

// set secure session cookie params sebelum session_start()
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',         // ubah jika perlu (mis. 'example.com')
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'     // Lax cocok untuk login form; bisa 'Strict' jika sesuai
    ]);
    session_start();
}

// hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login.php');
    exit;
}

// ambil input
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$next = $_POST['next'] ?? ''; // optional: form login bisa mengirim hidden input 'next'
$csrf = $_POST['csrf'] ?? null; // optional: jika kamu menggunakan CSRF token di form

// OPTIONAL: cek CSRF bila form mengirim token
// Jika kamu menambahkan CSRF di form, pastikan saat render form kamu set:
// $_SESSION['csrf_login'] = bin2hex(random_bytes(32));
// dan di form <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_login']

if ($csrf !== null) {
    if (!isset($_SESSION['csrf_login']) || !hash_equals($_SESSION['csrf_login'], $csrf)) {
        // invalid CSRF
        header('Location: ../public/login.php?error=' . urlencode('Form tidak valid (CSRF).'));
        exit;
    }
    // optional: unset CSRF token so it can't be reused
    unset($_SESSION['csrf_login']);
}

// validasi input
if ($email === '' || $password === '') {
    header('Location: ../public/login.php?error=' . urlencode('Email & password wajib diisi'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../public/login.php?error=' . urlencode('Email tidak valid'));
    exit;
}

// ambil user berdasarkan email (prepared statement)
$stmt = mysqli_prepare($conn, "SELECT id, name, password_hash, role FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
    // koneksi / query error
    header('Location: ../public/login.php?error=' . urlencode('Terjadi kesalahan server'));
    exit;
}
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

// jika tidak ada baris
if (mysqli_stmt_num_rows($stmt) === 0) {
    mysqli_stmt_close($stmt);
    // Jangan beri tahu detail (keamanan)
    header('Location: ../public/login.php?error=' . urlencode('Email atau password salah'));
    exit;
}

// bind result dan fetch
mysqli_stmt_bind_result($stmt, $id, $name, $password_hash, $role);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// verifikasi password
if (!password_verify($password, $password_hash)) {
    header('Location: ../public/login.php?error=' . urlencode('Email atau password salah'));
    exit;
}

// berhasil login: regenerasi session id dan set session vars
session_regenerate_id(true);

$_SESSION['user_id'] = (int)$id;
$_SESSION['user_name'] = $name;
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = $role;
$_SESSION['last_login'] = date('Y-m-d H:i:s');

// optional: simpan last_login ke DB (audit) atau log login
// $stmt = mysqli_prepare($conn, "UPDATE users SET last_login = NOW() WHERE id = ?");
// $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();

// sanitize/validate $next untuk mencegah open-redirect
$redirect = '';
if (!empty($next)) {
    // hanya izinkan path internal (mulai dengan '/'), tolak URL lengkap
    $next = trim($next);
    $p = parse_url($next);
    if (
        (!isset($p['scheme']) && !isset($p['host'])) && // bukan absolute URL
        isset($next[0]) && $next[0] === '/' &&         // relative path dimulai '/'
        strpos($next, '//') === false                    // double-slash suspicious
    ) {
        $redirect = $next;
    }
}

// default redirect berdasarkan role
if ($redirect) {
    header('Location: ' . $redirect);
    exit;
}

if ($role === 'admin') {
    header('Location: ../public/admin/admin_dashboard.php');
    exit;
} else {
    header('Location: ../public/users/users_dashboard.php');
    exit;
}
