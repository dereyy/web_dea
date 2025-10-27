<?php
// app/process_update_role.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';              // cek admin
require_once __DIR__ . '/../config/config.php';  // koneksi DB

require_admin(); // hanya admin bisa ubah role

// pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pert6-web-blog/public/admin/role_management.php');
    exit;
}

// --- CSRF Check ---
$csrf = $_POST['csrf'] ?? '';
if (empty($csrf) || !isset($_SESSION['csrf_role_mgmt']) || !hash_equals($_SESSION['csrf_role_mgmt'], $csrf)) {
    $_SESSION['flash'] = 'Form tidak valid (CSRF).';
    header('Location: /pert6-web-blog/public/admin/role_management.php');
    exit;
}
unset($_SESSION['csrf_role_mgmt']); // token hanya 1x pakai

// --- Validasi input ---
$user_id  = (int)($_POST['user_id'] ?? 0);
$new_role = $_POST['role'] ?? '';

$allowed_roles = ['admin', 'editor', 'user'];
if ($user_id <= 0 || !in_array($new_role, $allowed_roles)) {
    $_SESSION['flash'] = 'Data tidak valid.';
    header('Location: /pert6-web-blog/public/admin/role_management.php');
    exit;
}

// --- Cegah admin ubah role dirinya sendiri ---
if ($user_id === (int)current_user_id()) {
    $_SESSION['flash'] = 'Anda tidak bisa mengubah role diri sendiri.';
    header('Location: /pert6-web-blog/public/admin/role_management.php');
    exit;
}

// --- Ambil role lama (optional, buat log) ---
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($old_role);
$stmt->fetch();
$stmt->close();

// --- Update role ---
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ? LIMIT 1");
$stmt->bind_param('si', $new_role, $user_id);
$ok = $stmt->execute();
$stmt->close();

// --- Simpan log  ---
// log untuk nge get data users yg lama untuk diganti atau
// di update ke role user yang baru
if ($ok) {
    $admin_id = (int)current_user_id();
    $log = $conn->prepare("
        INSERT INTO role_changes (admin_id, user_id, old_role, new_role, changed_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    if ($log) {
        $log->bind_param('iiss', $admin_id, $user_id, $old_role, $new_role);
        $log->execute();
        $log->close();
    }

    // alert message atau status message
    // axios
    $_SESSION['flash'] = "Role pengguna berhasil diubah menjadi $new_role.";
} else {
    // kalau misal gagal buat user atau akun baru maka
    $_SESSION['flash'] = "Gagal memperbarui role. Coba lagi.";
}

// --- Redirect balik ---
header('Location: /pert6-web-blog/public/admin/role_management.php');
exit;
