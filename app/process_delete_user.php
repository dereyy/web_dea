<?php
// app/process_delete_user.php — disabled because delete-user feature removed
if (session_status() === PHP_SESSION_NONE) session_start();
header('Location: /pert6-web-blog/public/admin/role_management.php');
exit;
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['flash'] = 'User tidak ditemukan.';
    header('Location: /pert6-web-blog/public/admin/role_management.php');
    exit;
}

/* ========== Option A: Permanent delete ========== */
$stmt = $conn->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
if (!$stmt) {
    $_SESSION['flash'] = 'Gagal menghapus user (prepare).';
    header('Location: /pert6-web-blog/public/admin/role_management.php');
    exit;
}
$stmt->bind_param('i', $user_id);
$ok = $stmt->execute();
$stmt->close();

/* ========== Option B (safer): soft-delete alternative ==========
   Instead of deleting permanently, you can add column `deleted_at` and run:
   $stmt = $conn->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ? LIMIT 1");
   ...
   Then ensure all SELECTs include `WHERE deleted_at IS NULL`.
*/

// optional: audit log
if ($ok) {
    // create table user_deletes if desired, see SQL below
    $admin_id = (int)current_user_id();
    $log = $conn->prepare("INSERT INTO user_deletes (admin_id, user_id, user_name, user_email, deleted_at) VALUES (?, ?, ?, ?, NOW())");
    if ($log) {
        $log->bind_param('iiss', $admin_id, $user_id, $user['name'], $user['email']);
        $log->execute();
        $log->close();
    }

    $_SESSION['flash'] = 'User berhasil dihapus.';
} else {
    $_SESSION['flash'] = 'Gagal menghapus user: ' . $conn->error;
}

header('Location: /pert6-web-blog/public/admin/role_management.php');
exit;
