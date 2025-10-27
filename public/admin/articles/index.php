<?php
// public/admin/articles/index.php
require_once __DIR__ . '/../../../app/auth.php';
require_admin();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../app/functions.php';

// --- search + pagination setup (letakkan di bagian atas, sebelum query SELECT/COUNT) ---
$perPage = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// ambil keyword dari GET
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$whereSql = '';
$like = '';
if ($searchQuery !== '') {
  $whereSql = "WHERE (a.title LIKE ? OR a.content LIKE ?)";
  $like = '%' . $searchQuery . '%';
}

// COUNT total (dengan search)
$sqlCount = "SELECT COUNT(*) AS cnt FROM articles a $whereSql";
$stmtCount = mysqli_prepare($conn, $sqlCount);
if ($searchQuery !== '') {
  mysqli_stmt_bind_param($stmtCount, 'ss', $like, $like);
}
mysqli_stmt_execute($stmtCount);
$resCount = mysqli_stmt_get_result($stmtCount);
$row = mysqli_fetch_assoc($resCount);
$total = (int)($row['cnt'] ?? 0);
mysqli_stmt_close($stmtCount);

$totalPages = (int) ceil(max(1, $total) / $perPage);

// SELECT data (dengan search + limit)
$sql = "SELECT a.id, a.title, a.slug, a.featured_image, a.created_at, u.name AS author
        FROM articles a
        LEFT JOIN users u ON a.author_id = u.id
        $whereSql
        ORDER BY a.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
if ($searchQuery !== '') {
  // bind: search, search, limit, offset
  mysqli_stmt_bind_param($stmt, "ssii", $like, $like, $perPage, $offset);
} else {
  mysqli_stmt_bind_param($stmt, "ii", $perPage, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$articles = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// include layout (sidebar/header)
include __DIR__ . '/../_header_admin.php';
include __DIR__ . '/../_sidebar_admin.php';
?>

<main class="flex-1 p-8">
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold mb-2">Kelola Artikel</h1>
        <div class="text-sm text-gray-500">Total artikel: <span class="font-medium"><?= $total ?></span></div>
      </div>
    </div>

    <!-- search + create (responsif) -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
      <!-- Form di kiri -->
      <form method="GET" action="" class="flex gap-2">
        <input type="search" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Cari artikel..." class="px-3 py-3 border rounded w-full md:w-64 text-sm" />
        <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded text-sm">Search</button>
        <?php if ($searchQuery !== ''): ?>
          <a href="index.php" class="px-3 py-2 border rounded text-sm text-gray-700 hover:bg-gray-50">Reset</a>
        <?php endif; ?>
      </form>

      <!-- Tombol Buat Artikel di kanan -->
      <a href="create.php" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">Buat Artikel</a>
    </div>

    <!-- nampilin semua tabel -->
    <?php if (count($articles) === 0): ?>
      <div class="bg-white p-6 rounded shadow">Belum ada artikel.</div>
    <?php else: ?>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thumbnail</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">author</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $no = $offset + 1; foreach ($articles as $a): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-600 align-top"><?= $no++ ?></td>

                <td class="px-4 py-3 align-top">
                  <div class="font-medium text-gray-800"><?= e($a['title']) ?></div>
                </td>

                <td class="px-4 py-3 align-top">
                  <?php if ($a['featured_image']): ?>
                    <img src="<?= e('../../' . $a['featured_image']) ?>" alt="" class="w-20 h-12 object-cover rounded">
                  <?php else: ?>
                    <div class="w-20 h-12 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">No image</div>
                  <?php endif; ?>
                </td>

                <td class="px-4 py-3 text-sm text-gray-700 align-top"><?= e($a['author'] ?? 'Unknown') ?></td>

                <td class="px-4 py-3 text-sm text-gray-600 align-top"><?= date('d M Y', strtotime($a['created_at'])) ?></td>

                <td class="px-4 py-3 text-center align-top">
                  <div class="inline-flex items-center gap-2">
                    <!-- edit -->
                    <a href="edit.php?id=<?= $a['id'] ?>" class="text-indigo-600 text-sm px-2 py-1 rounded hover:bg-indigo-50">
                      <img src="https://cdn-icons-png.flaticon.com/128/1828/1828270.png" 
                      alt="edit" class="w-5 h-5" />
                    </a>

                    <form action="delete.php" method="POST" onsubmit="return confirm('Hapus artikel ini?');" class="inline">
                      <input type="hidden" name="id" value="<?= $a['id'] ?>">
                      <!-- hapus -->
                      <button type="submit" class="text-red-600 text-sm px-2 py-1 rounded hover:bg-red-50">
                        <img src="https://cdn-icons-png.flaticon.com/128/11540/11540197.png" alt="hapus" class="w-5 h-5">
                      </button>
                    </form>

                    <!-- lihat -->
                    <a href="<?= '../../articles.php?slug=' . e($a['slug']) ?>" target="_blank" class="text-gray-600 text-sm px-2 py-1 rounded hover:bg-gray-100">
                      <img src="https://cdn-icons-png.flaticon.com/128/432/432637.png" alt="lihat" class="w-5 h-5">
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- pagination -->
      <?php if ($totalPages > 1): ?>

        <?php
        // helper untuk membangun url pagination sambil mempertahankan query string lain
        function admin_page_url($p) {
          $params = $_GET;
          $params['page'] = $p;
          return htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($params));
        }
        ?>

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-6 rounded-b-lg">
          <div class="text-sm text-gray-700">
            Menampilkan
            <span class="font-medium"><?= $offset + 1 ?></span> -
            <span class="font-medium"><?= min($offset + $perPage, $total) ?></span>
            dari
            <span class="font-medium"><?= $total ?></span> artikel
          </div>

          <nav class="inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
            <!-- Tombol Prev -->
            <?php if ($page > 1): ?>
              <a href="<?= admin_page_url($page - 1) ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 border border-gray-300 bg-white hover:bg-gray-50" aria-label="Previous">
                <span class="sr-only">Previous</span>
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                  <path d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06L5.47 10.53a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" />
                </svg>
              </a>
            <?php else: ?>
              <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-300 border border-gray-200 bg-gray-50 cursor-not-allowed">
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                  <path d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06L5.47 10.53a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" />
                </svg>
              </span>
            <?php endif; ?>

            <!-- Nomor Halaman (compact dengan elipsis) -->
            <?php
            // tampilkan maksimal window 5 halaman (current ±2)
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            if ($start > 1) {
              echo '<a href="' . admin_page_url(1) . '" class="px-3 py-2 text-sm text-gray-700 border border-gray-300 bg-white hover:bg-gray-50">1</a>';
              if ($start > 2) echo '<span class="px-3 py-2 text-sm text-gray-500 border border-gray-300 bg-white">...</span>';
            }

            for ($i = $start; $i <= $end; $i++):
              if ($i == $page):
            ?>
                <span class="relative z-10 inline-flex items-center border border-indigo-600 bg-indigo-600 px-3 py-2 text-sm font-semibold text-white"><?= $i ?></span>
              <?php else: ?>
                <a href="<?= admin_page_url($i) ?>" class="relative inline-flex items-center border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= $i ?></a>
              <?php endif; endfor;

            if ($end < $totalPages) {
              if ($end < $totalPages - 1) echo '<span class="px-3 py-2 text-sm text-gray-500 border border-gray-300 bg-white">...</span>';
              echo '<a href="' . admin_page_url($totalPages) . '" class="px-3 py-2 text-sm text-gray-700 border border-gray-300 bg-white hover:bg-gray-50">' . $totalPages . '</a>';
            }
            ?>

            <!-- Tombol Next -->
            <?php if ($page < $totalPages): ?>
              <a href="<?= admin_page_url($page + 1) ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 border border-gray-300 bg-white hover:bg-gray-50" aria-label="Next">
                <span class="sr-only">Next</span>
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                  <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" />
                </svg>
              </a>
            <?php else: ?>
              <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-300 border border-gray-200 bg-gray-50 cursor-not-allowed">
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5">
                  <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" />
                </svg>
              </span>
            <?php endif; ?>
          </nav>
        </div>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/../_footer_admin.php'; ?>
