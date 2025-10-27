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
  SELECT a.*, u.name AS author
  FROM articles a
  LEFT JOIN users u ON a.author_id = u.id
  WHERE a.slug = ?
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
      class="w-full h-72 object-cover rounded-lg mb-6 shadow"
    >
  <?php endif; ?>

  <!-- Isi artikel -->
  <div class="prose prose-indigo max-w-none">
    <?= $article['content'] /* tampilkan HTML dari TinyMCE */ ?>
  </div>

  <!-- Komentar Section (form + daftar komentar) -->
  <?php
    // ambil komentar untuk article ini (yang disetujui)
    $article_id = (int)$article['id'];
    $stmtC = $conn->prepare("
      SELECT c.id, c.body, c.created_at, c.user_id, u.name AS user_name
      FROM comments c
      LEFT JOIN users u ON u.id = c.user_id
      WHERE c.article_id = ? AND c.is_approved = 1
      ORDER BY c.created_at ASC
    ");
    $stmtC->bind_param("i", $article_id);
    $stmtC->execute();
    $comments = $stmtC->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtC->close();
  ?>

  <section id="comments" class="mt-10">
    <h3 class="text-lg font-semibold mb-3">Komentar (<?= e(count($comments)) ?>)</h3>

    <!-- Form komentar -->
    <?php if (!empty($_SESSION['user_id'])): ?>
      <form method="post" action="process_comment.php" class="mb-6">
        <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="article_id" value="<?= e($article_id) ?>">
        <input type="hidden" name="slug" value="<?= e($article['slug']) ?>">
        <textarea name="body" rows="3" class="w-full border rounded p-3" placeholder="Tulis komentar..." required></textarea>
        <div class="mt-2 flex items-center justify-between">
          <div class="text-sm text-gray-500">Jaga etika saat berkomentar ya 😊.</div>
          <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kirim</button>
        </div>
      </form>
    <?php else: ?>
      <div class="mb-6 text-sm text-gray-600">
        <a href="login.php" class="text-indigo-600 hover:underline">Login</a> untuk menulis komentar.
      </div>
    <?php endif; ?>

    <!-- List komentar -->
    <div class="space-y-4">
      <?php if (empty($comments)): ?>
        <div class="text-gray-600">Belum ada komentar. Jadilah yang pertama!</div>
      <?php else: ?>
        <?php foreach ($comments as $c): ?>
          <div class="bg-white p-4 rounded shadow-sm">
            <div class="flex items-start justify-between">
              <div>
                <div class="text-sm font-medium"><?= e($c['user_name'] ?? 'Guest') ?></div>
                <div class="text-xs text-gray-400"><?= e(date('d M Y H:i', strtotime($c['created_at']))) ?></div>
              </div>

              <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                <!-- tombol hapus (pemilik komentar) -->
                <form method="post" action="process_comment.php" class="ml-4">
                  <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="delete_id" value="<?= e($c['id']) ?>">
                  <button type="submit" name="action" value="delete" class="text-sm text-red-500">Hapus</button>
                </form>
              <?php endif; ?>
            </div>

            <div class="mt-3 text-gray-700 whitespace-pre-wrap"><?= e($c['body']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <!-- Tombol kembali -->
  <div class="mt-8">
    <a href="articles.php" class="text-indigo-600 hover:underline">← Kembali ke daftar artikel</a>
  </div>
</article>

<?php include __DIR__ . '/_footer.php'; ?>
