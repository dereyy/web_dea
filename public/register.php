<?php include __DIR__ . '/_header.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-10">
  <h2 class="text-2xl font-semibold mb-2">Daftar Akun</h2>
  <p class="text-sm text-gray-600 mb-6">Buat akun untuk bisa mengirim artikel atau komentar.</p>

  <form action="../app/process_register.php" method="POST" class="bg-white p-6 rounded-lg shadow space-y-4">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700">Nama lengkap</label>
      <input id="name" name="name" type="text" required class="mt-1 block w-full px-3 py-2 border rounded" />
    </div>

    <div>
      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
      <input id="email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 border rounded" />
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
      <input id="password" name="password" type="password" required minlength="6" class="mt-1 block w-full px-3 py-2 border rounded" />
      <p class="text-xs text-gray-400 mt-1">Minimal 6 karakter. Password akan disimpan sebagai hash (server-side).</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Daftar</button>
      <a href="login.php" class="text-sm text-gray-600">Sudah punya akun? Login</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/_footer.php'; ?>
