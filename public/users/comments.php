<?php
// public/users/comments.php
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/functions.php';
require_once __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_login();

$user_id = (int)$_SESSION['user_id'];

// ambil komentar user beserta judul post
$stmt = $conn->prepare("
  SELECT c.id, c.body, c.created_at, a.id AS article_id, a.title, a.slug
  FROM comments c
  JOIN articles a ON a.id = c.article_id
  WHERE c.user_id = ?
  ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$myComments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include __DIR__ . '/_header_users.php';
include __DIR__ . '/_sidebar_users.php';
?>

<main class="flex-1 p-8">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Komentar Saya</h1>

    <?php if (empty($myComments)): ?>
      <div class="bg-white p-6 rounded shadow text-gray-600">Kamu belum menulis komentar apa pun.</div>
    <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($myComments as $c): ?>
          <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between">
              <div>
                <a href="/public/singlepost.php?slug=<?= e($c['slug']) ?>" class="font-semibold text-indigo-600 hover:underline">
                  <?= e($c['title']) ?>
                </a>
                <div class="text-xs text-gray-400">Pada <?= e(date('d M Y H:i', strtotime($c['created_at']))) ?></div>
              </div>
              <form method="post" action="/public/process_comment.php">
                <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="delete_id" value="<?= e($c['id']) ?>">
                <button type="submit" name="action" value="delete" class="text-sm text-red-500">Hapus</button>
              </form>
            </div>
            <div class="mt-3 text-gray-700"><?= e($c['body']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/_footer_users.php'; ?>