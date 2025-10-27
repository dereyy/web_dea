<?php include __DIR__ . '/_header.php'; ?>

<section class="bg-indigo-50 py-12">
  <div class="max-w-4xl mx-auto px-4 text-center">
    <h1 class="text-3xl md:text-4xl font-extrabold text-indigo-700">Tentang Blog Kampus</h1>
    <p class="mt-3 text-gray-600">Platform artikel & pengumuman kampus — ruang berbagi pengetahuan, pengalaman kuliah, dan pengumuman kegiatan untuk mahasiswa & dosen.</p>
  </div>
</section>

<section class="max-w-5xl mx-auto px-4 py-12">
  <div class="grid md:grid-cols-2 gap-8 items-center">
    <div>
      <h2 class="text-2xl font-semibold text-gray-800">Visi & Misi</h2>
      <ul class="mt-4 space-y-3 text-gray-600">
        <li class="flex items-start">
          <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full mr-3">✓</span>
          <span><strong>Visi:</strong> Menjadi sumber belajar & update kegiatan kampus yang dapat diandalkan oleh mahasiswa.</span>
        </li>
        <li class="flex items-start">
          <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full mr-3">✓</span>
          <span><strong>Misi:</strong> Mendorong kolaborasi penulisan artikel, dokumentasi kegiatan, dan berbagi tips akademik.</span>
        </li>
      </ul>
    </div>
    <div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-800">Kenapa ada kampus ini?</h3>
        <p class="mt-3 text-sm text-gray-600">Sebagai project latihan peserta pelatihan Junior Web Programming untuk praktik pembuatan web, sekaligus wadah konten kampus.</p>
      </div>
    </div>
  </div>
</section>

<!-- tim kontributor -->
<section class="bg-white py-12">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-2xl font-semibold text-gray-800">Tim & Kontributor</h2>
    <p class="mt-2 text-gray-600">Tim inti dan kontributor yang sering menulis di blog ini.</p>

    <div class="mt-6 grid sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div class="bg-gray-50 p-4 rounded-lg flex items-center space-x-4 shadow-sm">
        <img src="/assets/team-alia.jpg" alt="Alia" class="w-14 h-14 rounded-full object-cover">
        <div>
          <h4 class="text-sm font-medium text-gray-800">Alia Ramadhani</h4>
          <p class="text-xs text-gray-500">Editor & Content</p>
          <div class="mt-1 text-xs text-indigo-600">alia@kampus.ac.id</div>
        </div>
      </div>

      <div class="bg-gray-50 p-4 rounded-lg flex items-center space-x-4 shadow-sm">
        <img src="/assets/team-budi.jpg" alt="Budi" class="w-14 h-14 rounded-full object-cover">
        <div>
          <h4 class="text-sm font-medium text-gray-800">Budi Santoso</h4>
          <p class="text-xs text-gray-500">Developer & Ops</p>
          <div class="mt-1 text-xs text-indigo-600">budi@kampus.ac.id</div>
        </div>
      </div>

      <!-- tambah card lain sesuai kebutuhan -->
    </div>
  </div>
</section>

<section class="py-12 bg-indigo-50">
  <div class="max-w-5xl mx-auto px-4 text-center">
    <h2 class="text-2xl font-semibold text-gray-800">Angka Singkat</h2>
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-3xl font-bold text-indigo-700">120+</div>
        <div class="text-sm text-gray-600 mt-1">Artikel</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-3xl font-bold text-indigo-700">35</div>
        <div class="text-sm text-gray-600 mt-1">Kontributor</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-3xl font-bold text-indigo-700">8</div>
        <div class="text-sm text-gray-600 mt-1">Kategori</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-3xl font-bold text-indigo-700">5k</div>
        <div class="text-sm text-gray-600 mt-1">Pembaca / bulan</div>
      </div>
    </div>
  </div>
</section>

<section class="max-w-4xl mx-auto px-4 py-12">
  <h2 class="text-2xl font-semibold text-gray-800">Sejarah Singkat</h2>
  <ol class="mt-6 border-l-2 border-indigo-100">
    <li class="mb-6 ml-6">
      <span class="text-sm text-indigo-600 font-medium">Jan 2024</span>
      <h3 class="text-lg font-medium text-gray-800">Inisiasi project</h3>
      <p class="text-sm text-gray-600 mt-1">Dimulai sebagai project latihan kursus pembuatan web dan penulisan konten.</p>
    </li>
    <li class="mb-6 ml-6">
      <span class="text-sm text-indigo-600 font-medium">Mar 2024</span>
      <h3 class="text-lg font-medium text-gray-800">Peluncuran versi beta</h3>
      <p class="text-sm text-gray-600 mt-1">Mulai menerima kontribusi dari mahasiswa dan dosen.</p>
    </li>
  </ol>
</section>

<section class="bg-white py-12">
  <div class="max-w-3xl mx-auto px-4 text-center">
    <h2 class="text-2xl font-semibold text-gray-800">Ingin Berkontribusi?</h2>
    <p class="mt-2 text-gray-600">Kirim artikel, ide, atau laporkan kegiatan kampus melalui email kami.</p>
    <div class="mt-6 flex justify-center">
      <a href="mailto:editor@kampus.ac.id" class="inline-block px-6 py-3 rounded-lg bg-indigo-600 text-white font-medium shadow hover:bg-indigo-700">Kirim Artikel</a>
    </div>
  </div>
</section>

<section class="py-12 bg-indigo-50">
  <div class="max-w-4xl mx-auto px-4">
    <h2 class="text-xl font-semibold text-gray-800">Kontak & Sosial</h2>
    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="text-gray-600">
        <div>Email: <a href="mailto:info@kampus.ac.id" class="text-indigo-600">info@kampus.ac.id</a></div>
        <div class="mt-1">Instagram: <a href="#" class="text-indigo-600">@kampusepic</a></div>
      </div>
      <div>
        <span class="text-sm text-gray-500">Credits: TailwindCSS • PHP</span>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('section').forEach((s, i) => {
      s.classList.remove('opacity-0');
      s.classList.add('transition-opacity', 'duration-600', 'opacity-100');
    });
  });
</script>
