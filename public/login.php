<?php include __DIR__ . '/_header.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-10">
  <h2 class="text-2xl font-semibold mb-2">Login</h2>
  <p class="text-sm text-gray-600 mb-6">Masuk untuk mengelola portofolio atau mengirim komentar.</p>

  <form action="../app/process_login.php" method="POST" class="bg-white p-6 rounded-lg shadow space-y-4">
    <div>
      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
      <input id="email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 border rounded" />
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
      <input id="password" name="password" type="password" required class="mt-1 block w-full px-3 py-2 border rounded" />
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Login</button>
      <a href="register.php" class="text-sm text-gray-600">Belum punya akun? Daftar</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/_footer.php'; ?>