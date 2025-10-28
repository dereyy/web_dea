<?php
// public/articles.php (UI texts adapted to Portofolio)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/functions.php';
require_once __DIR__ . '/../app/auth.php';

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

// pagination
$perPage = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// build where + params for search
$whereSql = '';
$params = [];
$types = '';
if ($searchQuery !== '') {
  $whereSql = "WHERE (title LIKE ? OR content LIKE ?)";
  $like = '%' . $searchQuery . '%';
  $params[] = $like;
  $params[] = $like;
  $types .= 'ss';
}

// count total
$sqlCount = "SELECT COUNT(*) AS cnt FROM portofolio $whereSql";
$stmt = mysqli_prepare($conn, $sqlCount);
if ($whereSql) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
$totalItems = (int)($row['cnt'] ?? 0);
mysqli_stmt_close($stmt);

$totalPages = (int) max(1, ceil($totalItems / $perPage));

// fetch articles with limit
$sql = "SELECT p.id, p.title, p.slug, p.featured_image, p.content, p.created_at, u.name AS author
  FROM portofolio p
  LEFT JOIN users u ON p.author_id = u.id
  $whereSql
  ORDER BY p.created_at DESC
  LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);

if ($whereSql) {
  // dynamic bind: first the search params, then i i for limit offset
  mysqli_stmt_bind_param($stmt, $types . 'ii', ...array_merge($params, [$perPage, $offset]));
} else {
  mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$articles = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
/* NOTE: variable names below reference 'articles' DB table and columns —
  we only change UI labels to "Portofolio". Keeping DB fields intact. */

// helper untuk membangun url pagination sambil mempertahankan q
function build_page_url($p)
{
  $params = $_GET;
  $params['page'] = $p;
  return htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($params));
}

// LIKES AND COMMENTS
// ambil IDs artikel yg tampil
$articleIds = array_map(fn($r) => (int)$r['id'], $articles);
$likesCount = [];
$userLiked = [];
// comments feature removed: tidak lagi mengambil jumlah komentar

if (!empty($articleIds)) {
  $in = implode(',', $articleIds); // aman karena cast int

  // likes count
  $sqlCounts = "SELECT portofolio_id AS pid, COUNT(*) AS cnt FROM likes WHERE portofolio_id IN ($in) GROUP BY portofolio_id";
  $res = mysqli_query($conn, $sqlCounts);
  while ($row = mysqli_fetch_assoc($res)) {
    $likesCount[(int)$row['pid']] = (int)$row['cnt'];
  }
  mysqli_free_result($res);

  // komentar dinonaktifkan: tidak mengambil jumlah komentar

  // jika user login, ambil liked article id yang user miliki
  $currentUserId = $_SESSION['user_id'] ?? null;
  if ($currentUserId) {
    $sqlUserLikes = "SELECT portofolio_id AS pid FROM likes WHERE user_id = ? AND portofolio_id IN ($in)";
    $stmtL = mysqli_prepare($conn, $sqlUserLikes);
    mysqli_stmt_bind_param($stmtL, 'i', $currentUserId);
    mysqli_stmt_execute($stmtL);
    $res2 = mysqli_stmt_get_result($stmtL);
    while ($rl = mysqli_fetch_assoc($res2)) {
      $userLiked[(int)$rl['pid']] = true;
    }
    mysqli_stmt_close($stmtL);
  }
}


include __DIR__ . '/_header.php';
?>

<!-- Search + header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4
 mt-20">
  <h2 class="text-2xl font-semibold text-gray-800">Portofolio</h2>

  <form method="GET" action="articles.php" class="flex gap-2 w-full md:w-auto">
    <input
      type="search"
      name="q"
      value="<?= htmlspecialchars($searchQuery) ?>"
      placeholder="Cari portofolio..."
      class="flex-grow md:flex-none px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 text-gray-700 placeholder-gray-400 transition" />
    <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-lg shadow-md font-medium transition">Cari</button>
  </form>
</div>

<!-- card artikel -->
<div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
  <?php if (count($articles) > 0): ?>
    <?php foreach ($articles as $a): ?>
      <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition border border-gray-100 overflow-hidden">
        <?php if ($a['featured_image'] && file_exists(__DIR__ . '/' . $a['featured_image'])): ?>
          <img src="<?= e($a['featured_image']) ?>" alt="<?= e($a['title']) ?>" class="w-full h-44 object-cover">
        <?php else: ?>
          <div class="w-full h-44 bg-gray-100 flex items-center justify-center text-gray-400">No image</div>
        <?php endif; ?>

        <div class="p-5">
          <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition">
            <a href="singlepost.php?slug=<?= urlencode($a['slug']) ?>"><?= e($a['title']) ?></a>
          </h3>

          <div class="text-sm text-gray-400"><?= e($a['author'] ?? 'Admin') ?></div>
          <p class="text-sm text-gray-500 mt-1"><?= date('d M Y', strtotime($a['created_at'])) ?></p>

          <!-- excerpt: ambil dari content (strip tags) -->
          <?php
          // jika kamu menyimpan excerpt terpisah, gunakan itu. Kalau tidak:
          $excerpt = strip_tags($a['content'] ?? '');
          $excerpt = mb_substr($excerpt, 0, 160);
          ?>
          <p class="text-sm text-gray-600 mt-3 line-clamp-3"><?= e($excerpt) ?><?= (mb_strlen($excerpt) >= 160 ? '...' : '') ?></p>

          <!-- like -->
          <?php
          $aid = (int)$a['id'];
          $countLikes = $likesCount[$aid] ?? 0;
          $countComments = 0; // komentar dinonaktifkan
          $hasLiked = !empty($userLiked[$aid]);
          ?>

          <!-- Instagram-style minimal footer -->
          <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <?php if (!empty($_SESSION['user_id'])): ?>
                <form method="post" action="process_like.php" class="inline">
                  <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="article_id" value="<?= e($aid) ?>">
                  <button type="submit" aria-label="<?= $hasLiked ? 'Unlike' : 'Like' ?>"
                    class="flex items-center text-sm transition focus:outline-none <?= $hasLiked ? 'text-red-500' : 'text-gray-600 hover:text-red-500' ?>">
                    <!-- heart -->
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343 3.172 11.515a4 4 0 010-5.656z" />
                    </svg>
                    <span class="text-sm"><?= e($countLikes) ?></span>
                  </button>
                </form>
              <?php else: ?>
                <a href="login.php" class="flex items-center text-sm text-gray-600 hover:text-indigo-600">
                  <svg class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343 3.172 11.515a4 4 0 010-5.656z" />
                  </svg>
                  <span><?= e($countLikes) ?></span>
                </a>
              <?php endif; ?>

              <!-- komentar dinonaktifkan: ikon komentar dihapus -->
            </div>

            <!-- author or date (optional small text) -->
          </div>
          <!-- like -->

        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="col-span-full">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <!-- logged-in user sees actionable guidance when DB is empty -->
        <div class="bg-white p-6 rounded-xl text-center shadow border border-gray-100">
          <h3 class="text-lg font-medium text-gray-800">Belum ada portofolio</h3>
          <p class="mt-2 text-sm text-gray-600">Sepertinya belum ada portofolio di situs ini.</p>
          <p class="mt-3 text-sm text-gray-600">Anda sedang masuk. Anda dapat membuat portofolio pertama Anda sekarang.</p>
          <div class="mt-4">
            <a href="/public/users/portofolio/create.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">Buat Portofolio Baru</a>
          </div>
        </div>
      <?php else: ?>
        <!-- not logged in: show gentle info but keep section visible -->
        <div class="bg-white p-6 rounded-xl text-center shadow border border-gray-100">
          <h3 class="text-lg font-medium text-gray-800">Tidak ada portofolio ditemukan</h3>
          <p class="mt-2 text-sm text-gray-600">Belum ada portofolio. Silakan coba lagi nanti atau masuk untuk membuat portofolio Anda sendiri.</p>
          <div class="mt-4">
            <a href="login.php" class="inline-block text-indigo-600 underline">Masuk</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- pagination -->
<?php if ($totalPages > 1): ?>
  <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-10">
    <!-- Mobile -->
    <div class="flex flex-1 justify-between sm:hidden">
      <?php if ($page > 1): ?>
        <a href="<?= build_page_url($page - 1) ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
      <?php else: ?>
        <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Previous</span>
      <?php endif; ?>

      <?php if ($page < $totalPages): ?>
        <a href="<?= build_page_url($page + 1) ?>" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
      <?php else: ?>
        <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Next</span>
      <?php endif; ?>
    </div>

    <!-- Desktop -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium"><?= $offset + 1 ?></span>
          to
          <span class="font-medium"><?= min($offset + $perPage, $totalItems) ?></span>
          of
          <span class="font-medium"><?= $totalItems ?></span>
          results
        </p>
      </div>

      <div>
        <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-xs">
          <!-- Prev icon -->
          <?php if ($page > 1): ?>
            <a href="<?= build_page_url($page - 1) ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 inset-ring inset-ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0" aria-label="Previous">
              <span class="sr-only">Previous</span>
              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                <path d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" fill-rule="evenodd" />
              </svg>
            </a>
          <?php else: ?>
            <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-300 bg-white" aria-hidden="true">
              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                <path d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" fill-rule="evenodd" />
              </svg>
            </span>
          <?php endif; ?>

          <!-- Page numbers (compact with ellipsis) -->
          <?php
          $pages = [];
          if ($totalPages <= 7) {
            for ($i = 1; $i <= $totalPages; $i++) $pages[] = $i;
          } else {
            $pages[] = 1;
            $pages[] = 2;
            $start = max(3, $page - 1);
            $end   = min($totalPages - 2, $page + 1);
            if ($start > 3) $pages[] = '...';
            for ($i = $start; $i <= $end; $i++) $pages[] = $i;
            if ($end < $totalPages - 2) $pages[] = '...';
            $pages[] = $totalPages - 1;
            $pages[] = $totalPages;
          }

          // preserve order + remove duplicates (but keep '...' entries)
          $seen = [];
          $pages = array_values(array_filter(array_map(function ($p) use (&$seen) {
            if ($p === '...') return $p;
            if (!isset($seen[$p])) {
              $seen[$p] = true;
              return $p;
            }
            return null;
          }, $pages)));
          ?>

          <?php foreach ($pages as $p): ?>
            <?php if ($p === '...'): ?>
              <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 inset-ring inset-ring-gray-300">...</span>
            <?php else: ?>
              <?php if ($p == $page): ?>
                <a href="<?= build_page_url($p) ?>" aria-current="page" class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"><?= $p ?></a>
              <?php else: ?>
                <a href="<?= build_page_url($p) ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"><?= $p ?></a>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>

          <!-- Next icon -->
          <?php if ($page < $totalPages): ?>
            <a href="<?= build_page_url($page + 1) ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 inset-ring inset-ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0" aria-label="Next">
              <span class="sr-only">Next</span>
              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
              </svg>
            </a>
          <?php else: ?>
            <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-300 bg-white" aria-hidden="true">
              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
              </svg>
            </span>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  </div>
<?php endif; ?>



<?php include __DIR__ . '/_footer.php'; ?>