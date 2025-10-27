<?php include __DIR__ . '/_header.php'; ?>

<?php
// Dummy articles (bisa diganti dari DB nanti)
$articles = [
  [
    "title" => "Belajar Dasar PHP untuk Pemula",
    "date" => "2025-10-20",
    "excerpt" => "Panduan lengkap untuk memulai belajar bahasa pemrograman PHP dari dasar. Cocok untuk peserta pelatihan web programming.",
    "image" => "https://images.unsplash.com/photo-1607706189992-eae578626c86?auto=format&fit=crop&q=80&w=1170",
  ],
  [
    "title" => "Apa Itu Tailwind CSS?",
    "date" => "2025-10-18",
    "excerpt" => "Tailwind CSS adalah utility-first framework yang membuat proses styling lebih cepat dan efisien.",
    "image" => "https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1170",
  ],
  [
    "title" => "Menghubungkan PHP dengan Database MySQL",
    "date" => "2025-10-15",
    "excerpt" => "Tutorial langkah demi langkah cara koneksi PHP ke MySQL menggunakan mysqli dan PDO.",
    "image" => "https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&q=80&w=1170",
  ],
];
$recent = array_slice($articles, 0, 3);
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
      <a href="contact.php" class="inline-block mt-6 bg-indigo-600  py-3shadow hover:bg-indigo-600 text-white px-4 py-2 rounded">Portofolio</a>
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
    <?php foreach ($recent as $a): ?>
      <article class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <a href="#" class="block h-48">
          <img src="<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['title']) ?>" class="w-full h-full object-cover">
        </a>
        <div class="p-5">
          <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600">
            <a href="#"><?= htmlspecialchars($a['title']) ?></a>
          </h3>
          <p class="text-xs text-gray-500 mt-1"><?= date('d M Y', strtotime($a['date'])) ?></p>
          <p class="text-sm text-gray-600 mt-3 line-clamp-3"><?= htmlspecialchars($a['excerpt']) ?></p>
          <div class="mt-4 flex items-center justify-between">
            <a href="#" class="text-indigo-600 font-medium text-sm hover:underline">Baca Selengkapnya →</a>
            <span class="text-xs text-gray-400">• 3 min read</span>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
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

<!-- CALL TO ACTION / Social -->
<section id="contact" class="mb-16">
  <div class="bg-white rounded-lg shadow-md p-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
      <h3 class="text-lg font-semibold text-gray-800">Tetap Terhubung</h3>
      <p class="text-sm text-gray-600 mt-1">Ikuti update artikel dan pengumuman terbaru—boleh juga kirimkan pertanyaan lewat kontak.</p>
    </div>

    <div class="flex items-center gap-3">
      <!-- Instagram -->
      <a href="https://instagram.com/yourhandle" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full  from-pink-500 to-yellow-400 shadow hover:scale-110 transition-transform" aria-label="Instagram">
        <img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" alt="Instagram" class="w-5 h-5">
      </a>

      <!-- WhatsApp -->
      <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full  shadow hover:scale-110 transition-transform" aria-label="WhatsApp">
        <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp" class="w-5 h-5">
      </a>

      <!-- Twitter -->
      <a href="https://twitter.com/yourhandle" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full shadow hover:scale-110 transition-transform" aria-label="Twitter">
        <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter" class="w-5 h-5">
      </a>

      <!-- Facebook -->
      <a href="https://facebook.com/yourpage" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full shadow hover:scale-110 transition-transform" aria-label="Facebook">
        <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook" class="w-5 h-5">
      </a>
    </div>
  </div>
</section>

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