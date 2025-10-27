<?php
// public/users/comments.php — replaced: fitur komentar dinonaktifkan
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/functions.php';
require_once __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

include __DIR__ . '/_header_users.php';
include __DIR__ . '/_sidebar_users.php';
?>

<main class="flex-1 p-8">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Komentar</h1>
    <div class="bg-white p-6 rounded shadow text-gray-700">
      Fitur komentar telah dinonaktifkan pada situs ini. Semua halaman dan fungsi terkait komentar telah dihapus atau dialihkan.
      <div class="mt-4">
        <a href="users_dashboard.php" class="text-indigo-600 hover:underline">Kembali ke Dashboard</a>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/_footer_users.php'; ?>