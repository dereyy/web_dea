<!-- admin dashboard -->
<?php
include __DIR__ . '../../../app/auth.php';
require_admin();
include __DIR__ . '/_header_admin.php';
include __DIR__ . '/_sidebar_admin.php';
?>

<!-- main content dashboard -->
<main class="flex-1 p-10">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-2">Admin Dashboard</h1>
    <p class="text-gray-600 mb-6">Selamat datang, <?= e(current_user_name()) ?> — role: <?= e(current_user_role()) ?></p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg p-6 shadow">
        <h3 class="font-semibold">Kelola Artikel</h3>
        <p class="text-sm text-gray-500 mt-2">Tambah / edit / hapus artikel.</p>
        <a href="#" class="text-indigo-600 mt-3 inline-block">Buka manajemen artikel →</a>
      </div>

      <div class="bg-white rounded-lg p-6 shadow">
        <h3 class="font-semibold">Kelola Komentar</h3>
        <p class="text-sm text-gray-500 mt-2">Review pesan atau komentar dari pengunjung.</p>
        <a href="#" class="text-indigo-600 mt-3 inline-block">Lihat komentar →</a>
      </div>
    </div>
  </div>
</main>
<!-- end of -->

<?php include __DIR__ . '/_footer_admin.php'; ?>
