<?php
$base = '/pert6-web-blog/public';

$avatar_url = "https://cdn-icons-png.flaticon.com/128/3177/3177440.png";

$user_name = $_SESSION['user_name'] ?? 'Admin Kampus';
$user_role = $_SESSION['user_role'] ?? 'admin';
?>
<aside class="w-64 bg-white border-r hidden md:block">
  <div class="p-6">
    <a href="<?= $base ?>/admin/admin_dashboard.php" class="text-lg font-semibold text-indigo-600 flex items-center gap-2">
      Portofolio Dea — Admin
    </a>
  </div>

  <nav class="px-4 py-6 space-y-2 text-gray-700">
    <a href="<?= $base ?>/admin/admin_dashboard.php"
      class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 <?= basename($_SERVER['PHP_SELF']) === 'admin_dashboard.php' ? 'bg-indigo-50 font-semibold' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8" />
      </svg>
      Dashboard
    </a>

    <a href="<?= $base ?>/admin/role_management.php" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8m-8-4h8M4 6h16v12H4z" />
      </svg>
      Manajemen Akun
    </a>

    <a href="<?= $base ?>/admin/portofolio/index.php" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8m-8-4h8M4 6h16v12H4z" />
      </svg>
      Kelola Portofolio
    </a>

    <a href="<?= $base ?>/logout.php" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-red-600">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
      </svg>
      Logout
    </a>
  </nav>

  <!-- Profile block removed as requested -->

  <!-- back to home -->
  <div class="border-t p-4 text-sm text-gray-600 space-y-3">
    <a href="<?= $base ?>/index.php" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m0 0l2 2m-2-2v8a2 2 0 01-2 2H7a2 2 0 01-2-2v-8z" />
      </svg>
      Kembali Halaman Utama
    </a>
  </div>
  <!-- end -->
</aside>