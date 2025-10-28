<?php
// public/singlepost.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/functions.php';
require_once __DIR__ . '/../app/auth.php'; // pastikan auth/session tersedia

// ambil slug dari URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug === '') {
  // redirect ke daftar artikel kalau slug kosong
  header('Location: articles.php');
  exit;
}

// ambil artikel dari database
$stmt = mysqli_prepare($conn, "

  SELECT p.*, u.name AS author
  FROM portofolio p
  LEFT JOIN users u ON p.author_id = u.id
  WHERE p.slug = ?
  LIMIT 1
");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$article = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

// kalau artikel tidak ditemukan
if (!$article) {
  include __DIR__ . '/_header.php';
  echo '<div class="max-w-3xl mx-auto py-10 text-center">
          <h2 class="text-2xl font-semibold text-gray-700">Artikel tidak ditemukan</h2>
          <a href="articles.php" class="text-indigo-600 hover:underline">← Kembali ke daftar artikel</a>
        </div>';
  include __DIR__ . '/_footer.php';
  exit;
}

// tampilkan halaman artikel
include __DIR__ . '/_header.php';
?>

<article class="max-w-4xl mx-auto py-10">
  <!-- Judul -->
  <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= e($article['title']) ?></h1>

  <!-- Info author & tanggal -->
  <div class="text-sm text-gray-500 mb-6">
    Oleh <span class="font-medium"><?= e($article['author'] ?? 'Admin') ?></span>
    • <?= date('d M Y', strtotime($article['created_at'])) ?>
  </div>

  <!-- Gambar utama -->
  <?php if (!empty($article['featured_image']) && file_exists(__DIR__ . '/' . $article['featured_image'])): ?>
    <img
      src="<?= e($article['featured_image']) ?>"
      alt="<?= e($article['title']) ?>"
      class="w-full h-72 object-cover rounded-lg mb-6 shadow">
  <?php endif; ?>

  <!-- Isi artikel -->
  <div class="prose prose-indigo max-w-none">
    <?= $article['content'] /* tampilkan HTML dari TinyMCE */ ?>
  </div>

  <!-- Komentar Section (form + daftar komentar) -->
  <!-- Komentar dinonaktifkan: fitur komentar telah dihapus -->

  <!-- Tombol kembali -->
  <div class="mt-8">
    <a href="articles.php" class="text-indigo-600 hover:underline">← Kembali ke daftar artikel</a>
  </div>
</article>

<?php include __DIR__ . '/_footer.php'; ?>