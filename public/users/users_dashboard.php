<?php
// include middleware untuk authentication nya
require_once __DIR__ . '/../../app/auth.php';
require_login();

// include layout
include __DIR__ . '/_header_users.php';
include __DIR__ . '/_sidebar_users.php';
?>
<!-- untuk nampilin dashboard user -->
<main class="flex-1 p-8">
  <div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold">User Dashboard</h1>
    <p class="text-gray-600">Halo, <?= e(current_user_name()) ?>. Ini halaman user</p>
    <div class="mt-6 bg-white p-6 rounded shadow">
      <!-- get session account berdasarkan role nya -->
      <p>Email: <?= e($_SESSION['user_email'] ?? '') ?></p>
      <p>Role: <?= e(current_user_role()) ?></p>
    </div>
  </div>
</main>
<?php include __DIR__ . '/_footer_users.php'; // bisa pakai same footer as admin atau buat users/_footer.php ?>
