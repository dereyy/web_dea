<?php
// public/singlepost.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/functions.php';
require_once __DIR__ . '/../app/auth.php'; // pastikan auth/session tersedia

// ambil slug dari URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug === '') {
  // redirect ke daftar portofolio kalau slug kosong
  header('Location: portofolio.php');
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
          <h2 class="text-2xl font-semibold text-gray-700">Portofolio tidak ditemukan</h2>
          <a href="portofolio.php" class="text-indigo-600 hover:underline">← Kembali ke daftar portofolio</a>
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

  <!-- Like button -->
  <div class="mt-6">
    <?php
    $currentUser = $_SESSION['user_id'] ?? null;
    $countLikes = 0;
    $hasLiked = false;
    if ($article) {
      $stmtL = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM likes WHERE portofolio_id = ?");
      mysqli_stmt_bind_param($stmtL, 'i', $article['id']);
      mysqli_stmt_execute($stmtL);
      $resL = mysqli_stmt_get_result($stmtL);
      $rL = mysqli_fetch_assoc($resL);
      $countLikes = (int)($rL['cnt'] ?? 0);
      mysqli_stmt_close($stmtL);

      if ($currentUser) {
        $stmt2 = mysqli_prepare($conn, "SELECT id FROM likes WHERE portofolio_id = ? AND user_id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt2, 'ii', $article['id'], $currentUser);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);
        $hasLiked = $res2->num_rows > 0;
        mysqli_stmt_close($stmt2);
      }
    }
    ?>

    <div class="flex items-center">
      <?php if ($currentUser): ?>
        <form method="POST" action="/web-dea/public/process_like.php" class="inline">
          <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="article_id" value="<?= e($article['id']) ?>">
          <button type="submit" class="px-3 py-2 rounded <?= $hasLiked ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-700' ?>">
            <?= $hasLiked ? 'Unlike' : 'Like' ?> (<?= $countLikes ?>)
          </button>
        </form>
      <?php else: ?>
        <a href="/web-dea/public/login.php" class="px-3 py-2 bg-gray-100 rounded">Login to like (<?= $countLikes ?>)</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tombol kembali -->
  <div class="mt-8">
    <a href="portofolio.php" class="text-indigo-600 hover:underline">← Kembali ke daftar portofolio</a>
  </div>
</article>

<?php include __DIR__ . '/_footer.php'; ?>