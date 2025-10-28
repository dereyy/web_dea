<?php
include __DIR__ . '/_header.php';

// ambil 3 portofolio terakhir dari database
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/functions.php';

$recent = [];
$dbError = null;
// prepared statement untuk keamanan
$sql = "SELECT p.id, p.title, p.slug, p.featured_image, p.content, p.created_at, u.name AS author
  FROM portofolio p
  LEFT JOIN users u ON p.author_id = u.id
  ORDER BY p.created_at DESC
  LIMIT 3";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
  $dbError = mysqli_error($conn);
} else {
  if (mysqli_stmt_execute($stmt)) {
    $res = mysqli_stmt_get_result($stmt);
    $recent = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
  } else {
    $dbError = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
  }
}
?>

<!-- HERO: Statis (mengganti carousel sesuai permintaan) -->
<section id="hero" class="mb-12 mt-20 bg-indigo-50 rounded-lg p-8">
  <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-8 px-4">
    <div class="md:w-2/5 text-left">
      <p class="text-sm text-gray-600 mb-2">Hey, I am Dea</p>
      <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight">
        I am <span class="text-indigo-700">Web Developer</span><br />
      </h1>
      <p class="mt-4 text-gray-600 max-w-xl">Exceptional website performance and design. Dea makes success feel effortless.</p>
      <a href="articles.php" class="inline-block mt-6 bg-indigo-600  py-3shadow hover:bg-indigo-600 text-white px-4 py-2 rounded">Portofolio</a>
    </div>

    <div class="md:w-3/5 flex justify-center md:justify-end">
      <div class="w-full max-w-3xl rounded-lg p-6 md:rounded-[40px] overflow-hidden">
        <img src="../image/home.png" alt="Home" class="w-full h-[420px] md:h-[520px] object-cover rounded-[34px]">
      </div>
    </div>
  </div>
</section>

<!-- RECENT POSTS (Portofolio) -->
<section id="portfolio" class="mb-12">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Portofolio Dea</h2>
    <a href="articles.php" class="text-indigo-600 hover:underline text-sm">Lihat Semua Portofolio →</a>
  </div>

  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <?php if (!empty($dbError)): ?>
      <div class="col-span-full bg-red-50 p-6 rounded-xl text-center border border-red-100">
        <h3 class="text-lg font-medium text-red-700">Terjadi kesalahan pada database</h3>
        <p class="mt-2 text-sm text-red-600">Silakan coba lagi nanti. Jika Anda admin, cek konfigurasi database.</p>
        <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <pre class="mt-3 text-xs text-red-600 bg-white p-2 rounded overflow-auto"><?= htmlspecialchars($dbError) ?></pre>
        <?php endif; ?>
      </div>
    <?php elseif (count($recent) > 0): ?>
      <?php foreach ($recent as $a): ?>
        <article class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
          <a href="singlepost.php?slug=<?= urlencode($a['slug']) ?>" class="block h-48">
            <?php if (!empty($a['featured_image']) && file_exists(__DIR__ . '/' . $a['featured_image'])): ?>
              <img src="<?= e($a['featured_image']) ?>" alt="<?= e($a['title']) ?>" class="w-full h-full object-cover">
            <?php else: ?>
              <?php
              // try to extract an image from content or use placeholder
              $imgUrl = $a['featured_image'] ?: '/image/placeholder.png';
              ?>
              <img src="<?= e($imgUrl) ?>" alt="<?= e($a['title']) ?>" class="w-full h-full object-cover">
            <?php endif; ?>
          </a>
          <div class="p-5">
            <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600">
              <a href="singlepost.php?slug=<?= urlencode($a['slug']) ?>"><?= e($a['title']) ?></a>
            </h3>
            <p class="text-xs text-gray-500 mt-1"><?= e(date('d M Y', strtotime($a['created_at']))) ?></p>
            <?php
            $excerpt = strip_tags($a['content'] ?? '');
            $excerpt = mb_substr($excerpt, 0, 140);
            ?>
            <p class="text-sm text-gray-600 mt-3 line-clamp-3"><?= e($excerpt) ?><?= (mb_strlen($excerpt) >= 140 ? '...' : '') ?></p>
            <div class="mt-4 flex items-center justify-between">
              <a href="singlepost.php?slug=<?= urlencode($a['slug']) ?>" class="text-indigo-600 font-medium text-sm hover:underline">Baca Selengkapnya →</a>
              <span class="text-xs text-gray-400">• 3 min read</span>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-span-full">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <div class="bg-white p-6 rounded-xl text-center shadow border border-gray-100">
            <h3 class="text-lg font-medium text-gray-800">Belum ada portofolio</h3>
            <p class="mt-2 text-sm text-gray-600">Sepertinya belum ada portofolio di situs ini.</p>
            <p class="mt-3 text-sm text-gray-600">Anda sedang masuk. Anda dapat membuat portofolio pertama Anda sekarang.</p>
            <div class="mt-4">
              <a href="/public/users/portofolio/create.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">Buat Portofolio Baru</a>
            </div>
          </div>
        <?php else: ?>
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
</section>

<!-- ABOUT -->
<section id="about" class="mb-12 bg-indigo-50 rounded-lg p-8">
  <div class="max-w-4xl mx-auto text-center">
    <h2 class="text-2xl font-semibold text-indigo-700">Tentang Dea</h2>
    <p class="mt-3 text-gray-700">
      Dea adalah mahasiswa UPN Veteran Yogyakarta (UPNVYK) semester 7 jurusan Informatika. Ia tertarik pada pengembangan web,
      desain antarmuka, dan pengalaman pengguna. Di portofolio ini, Dea menampilkan proyek-proyek tugas kuliah dan
      proyek pribadi yang memperlihatkan kemampuan dalam frontend, backend, dan desain.
    </p>
  </div>
</section>

<!-- Contact section removed as requested -->

<!-- Carousel script -->
<script>
  (function() {
    const slides = Array.from(document.querySelectorAll('#carousel .carousel-item'));
    const dots = Array.from(document.querySelectorAll('#carousel .carousel-dot'));
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let current = 0;
    let timer = null;
    const delay = 3500;

    function show(idx) {
      slides.forEach((s, i) => {
        if (i === idx) {
          s.style.opacity = '1';
          s.setAttribute('aria-hidden', 'false');
        } else {
          s.style.opacity = '0';
          s.setAttribute('aria-hidden', 'true');
        }
      });
      dots.forEach((d, i) => {
        d.classList.toggle('bg-white/90', i === idx);
        d.classList.toggle('bg-white/50', i !== idx);
      });
      current = idx;
    }

    function next() {
      show((current + 1) % slides.length);
    }

    function prev() {
      show((current - 1 + slides.length) % slides.length);
    }

    // auto play
    function startTimer() {
      timer = setInterval(next, delay);
    }

    function stopTimer() {
      if (timer) clearInterval(timer);
    }

    // events
    nextBtn.addEventListener('click', () => {
      stopTimer();
      next();
      startTimer();
    });
    prevBtn.addEventListener('click', () => {
      stopTimer();
      prev();
      startTimer();
    });
    dots.forEach(d => d.addEventListener('click', () => {
      stopTimer();
      show(parseInt(d.getAttribute('data-to')));
      startTimer();
    }));

    // pause on hover
    const carouselEl = document.getElementById('carousel');
    carouselEl.addEventListener('mouseenter', stopTimer);
    carouselEl.addEventListener('mouseleave', startTimer);

    // init
    show(0);
    startTimer();
  })();
</script>

<?php include __DIR__ . '/_footer.php'; ?>