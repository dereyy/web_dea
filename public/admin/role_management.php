<?php
// public/admin/role_management.php
require_once __DIR__ . '/../../app/auth.php';
require_admin();
require_once __DIR__ . '/../../config/config.php';

// CSRF token (single-use per page load)
if (empty($_SESSION['csrf_role_mgmt'])) {
    $_SESSION['csrf_role_mgmt'] = bin2hex(random_bytes(24));
}
$csrf_token = $_SESSION['csrf_role_mgmt'];

$flash = $_SESSION['flash'] ?? $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);

// ambil semua user
$sql = "SELECT id, name, email, role, created_at FROM users ORDER BY id ASC";
$result = $conn->query($sql);

$roles = ['admin', 'editor', 'user'];
$current_user_id = current_user_id(); // helper dari auth.php

// include layout partials (header/sidebar) — pastikan path benar
include __DIR__ . '/_header_admin.php';
include __DIR__ . '/_sidebar_admin.php';
?>

<!-- Main area -->
<main class="flex-1 p-8 bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Role Management</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar pengguna dan peran mereka. Hanya admin yang dapat mengubah role.</p>
      </div>
    </div>

    <!-- Flash -->
    <?php if ($flash): ?>
      <div class="mb-6 rounded-lg p-4 bg-green-50 border border-green-100">
        <p class="text-sm text-green-800"><?= strip_tags($flash) ?></p>
      </div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
      <div class="px-4 py-3 sm:px-6 flex items-center justify-between">
        <div class="text-sm text-gray-600">Total users: <span class="font-medium text-gray-800"><?= $result->num_rows ?></span></div>
        <!-- tambah user -->
         <p class="mt-4 text-right">
            <a href="/pert6-web-blog/public/admin/add_user.php" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Tambah User
            </a>
        </p>
        <!-- end -->
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-100">
            <!-- perulangan buat nampilin semua data users -->
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $row['id'] ?></td>

                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                  <div class="text-xs text-gray-500"><?= htmlspecialchars($row['created_at']) ?></div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['email']) ?></td>

                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php
                      // color label by role
                      if ($row['role'] === 'admin') echo 'bg-red-100 text-red-800';
                      elseif ($row['role'] === 'editor') echo 'bg-yellow-100 text-yellow-800';
                      else echo 'bg-gray-100 text-gray-800';
                    ?>">
                    <?= htmlspecialchars($row['role']) ?>
                  </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <?php if ($row['id'] != $current_user_id): ?>

                    <!-- form simpan dan update dan delete -->
                  <div class="flex items-center gap-3">
                    <!-- form update role -->
                    <form method="post" action="/pert6-web-blog/app/process_update_role.php" class="flex items-center gap-3">
                      <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                      <input type="hidden" name="csrf" value="<?= $csrf_token ?>">

                      <label class="sr-only">Pilih role</label>
                      <select name="role" required class="block w-36 rounded-md border border-gray-400 bg-white py-1.5 pl-3 pr-8 text-sm focus:ring-0 focus:border-indigo-500">
                        <?php foreach ($roles as $r): ?>
                          <option value="<?= $r ?>" <?= $r === $row['role'] ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                      </select>

                      <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Simpan
                      </button>
                    </form>

                    <!-- form delete user -->
                    <form method="post" action="/pert6-web-blog/app/process_delete_user.php" class="inline">
                      <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                      <input type="hidden" name="csrf" value="<?= $csrf_token ?>">
                      <button type="submit"
                        onclick="return confirm('Hapus user <?= addslashes(htmlspecialchars($row['name'])) ?> (<?= addslashes(htmlspecialchars($row['email'])) ?>)? Data tidak bisa dikembalikan.');"
                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                        Hapus
                      </button>
                    </form>
                  </div>
                    <!-- end form -->
                  <?php else: ?>
                    <div class="text-sm text-gray-500 italic">(Ini kamu)</div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div> <!-- /table scroll -->
    </div> <!-- /card -->

    <div class="mt-6 text-sm text-gray-600">
      <p>Catatan: perubahan role berlaku pada saat berikutnya user login (session tidak otomatis di-refresh).</p>
    </div>
  </div>
</main>

<?php include __DIR__ . '/_footer_admin.php'; ?>
