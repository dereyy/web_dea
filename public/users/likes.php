<?php
// public/users/likes.php
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/functions.php';
require_once __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

$user_id = (int)($_SESSION['user_id'] ?? 0);

// ambil semua artikel yang di-like user (lengkap)
$stmt = $conn->prepare("
  SELECT a.id, a.title, a.slug, a.featured_image, a.content, a.created_at, l.created_at AS liked_at
  FROM likes l
  JOIN articles a ON a.id = l.article_id
  WHERE l.user_id = ?
  ORDER BY l.created_at DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$liked = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include __DIR__ . '/_header_users.php';
include __DIR__ . '/_sidebar_users.php';
?>

<main class="flex-1 p-8">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-semibold mb-2">Postingan yang Kamu Sukai</h1>
    <p class="text-gray-600 mb-6">Daftar artikel yang pernah kamu beri <strong>Like</strong>. Kamu bisa membatalkan like dari sini.</p>

    <?php if (empty($liked)): ?>
      <div class="bg-white p-6 rounded shadow text-gray-600">Kamu belum menyukai artikel apapun.</div>
    <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($liked as $a): ?>
          <div class="bg-white rounded-lg shadow p-4 flex flex-col md:flex-row md:justify-between md:items-start">
            <div class="md:flex-1">
              <a href="../singlepost.php?slug=<?= urlencode($a['slug']) ?>" class="text-lg font-semibold text-indigo-600 hover:underline">
                <?= e($a['title']) ?>
              </a>
              <div class="text-sm text-gray-500">Dipublikasikan: <?= e(date('d M Y', strtotime($a['created_at']))) ?></div>
              <div class="text-xs text-gray-400 mt-1">Kamu menyukai: <?= e(date('d M Y H:i', strtotime($a['liked_at']))) ?></div>
              <?php
                // excerpt (opsional)
                $excerpt = mb_substr(strip_tags($a['content'] ?? ''), 0, 160);
              ?>
              <p class="text-sm text-gray-600 mt-2"><?= e($excerpt) ?><?= (mb_strlen($excerpt) >= 160 ? '...' : '') ?></p>
            </div>

            <div class="mt-4 md:mt-0 md:ml-6 flex-shrink-0">
              <!-- Unlike form: action relatif ke public/process_like.php -->
              <form method="post" action="../process_like.php">
                <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="article_id" value="<?= e($a['id']) ?>">
                <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-red-500 text-white hover:bg-red-600">
                  Unlike
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/_footer_users.php'; ?>
