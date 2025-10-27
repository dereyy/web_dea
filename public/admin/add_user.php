<?php
// public/admin/add_user.php
require_once __DIR__ . '/../../app/auth.php';
require_admin();
require_once __DIR__ . '/../../config/config.php';

// CSRF token
if (empty($_SESSION['csrf_add_user'])) {
    $_SESSION['csrf_add_user'] = bin2hex(random_bytes(24));
}
$csrf_token = $_SESSION['csrf_add_user'];

$flash = $_SESSION['flash'] ?? $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);

$roles = ['admin', 'editor', 'user'];

include __DIR__ . '/_header_admin.php';
include __DIR__ . '/_sidebar_admin.php';
?>

<main class="flex-1 bg-gray-50 min-h-screen p-8">
  <div class="max-w-4xl mx-auto">
    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-3xl font-extrabold text-gray-900">Tambah User Baru</h1>
      <p class="mt-2 text-sm text-gray-600">Buat akun baru dan pilih perannya (admin / editor / user).</p>
    </div>

    <!-- Flash -->
    <?php if ($flash): ?>
      <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-100">
        <p class="text-sm text-green-800"><?= htmlspecialchars($flash) ?></p>
      </div>
    <?php endif; ?>

    <!-- Form card (Tailwind-like form layout) -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-5 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800">Informasi Akun</h2>
        <p class="mt-1 text-sm text-gray-500">Isi data pengguna. Password minimal 6 karakter.</p>
      </div>

      <form action="/pert6-web-blog/app/process_create_user.php" method="post" class="px-6 py-6 space-y-6">
        <input type="hidden" name="csrf" value="<?= $csrf_token ?>">

        <!-- body isi tambah data -->
            <!-- Container form -->
            <div class="space-y-5">

            <!-- Nama lengkap -->
            <div class="flex items-center space-x-3">
                <label for="name" class="w-32 text-sm font-medium text-gray-700">Nama lengkap</label>
                <input id="name" name="name" type="text" required
                class="flex-1 max-w-sm rounded-md border border-gray-500 py-2 px-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Email -->
            <div class="flex items-center space-x-3">
                <label for="email" class="w-32 text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" placeholder="nama@gmail.com" required
                class="flex-1 max-w-sm rounded-md border border-gray-500 py-2 px-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <!-- Password -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3">
                <label for="password" class="w-32 text-sm font-medium text-gray-700">Password</label>
                <div class="flex-1">
                <input id="password" name="password" type="password" minlength="6" required
                    class="w-full max-w-sm rounded-md border border-gray-500 py-2 px-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p class="mt-2 text-xs text-gray-500">Minimal 6 karakter. (Admin dapat mengganti password nanti jika perlu.)</p>
                </div>
            </div>

            <!-- Role -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3">
                <label for="role" class="w-32 text-sm font-medium text-gray-700">Role</label>
                <div class="flex-1">
                <select id="role" name="role" required
                    class="w-48 rounded-md border border-gray-500 bg-white py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <?php foreach ($roles as $r): ?>
                    <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-2 text-xs text-gray-500">Pilih peran yang sesuai untuk akun ini.</p>
                </div>
            </div>
            </div>
        <!-- end -->

        <!-- tombol submit kembali -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <div class="flex items-center gap-5"> <!-- tambahin flex + gap di sini -->
        <button type="submit"
          class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
                Tambah User
            </button>

            <a href="/pert6-web-blog/public/admin/role_management.php"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-md hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 shadow-sm">
            Kembali
            </a>
        </div>
        </div>

            
          </div>
        </div>
      </form>
    </div> <!-- end card -->

    <p class="mt-6 text-sm text-gray-500">
      Catatan: jangan izinkan pengguna biasa memilih role saat registrasi. Hanya admin yang boleh membuat akun dengan role tertentu.
    </p>
  </div>
</main>

<?php include __DIR__ . '/_footer_admin.php'; ?>
