<?php
// contact.php
include __DIR__ . '/../config/config.php'; // jika perlu
$success = isset($_GET['success']) ? (int)$_GET['success'] : 0;
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<?php include __DIR__ . '/_header.php'; ?>

<!-- untuk kontak form -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="md:col-span-2">
    <h2 class="text-2xl font-semibold">Kontak</h2>
    <p class="mt-1 text-gray-600">Isi form berikut untuk mengirim pesan kepada admin.</p>

    <!-- status form message  -->
    <?php if ($success === 1): ?>
      <div class="mt-4 p-3 rounded bg-green-50 border border-green-200 text-green-700">Terima kasih — pesan Anda sudah terkirim.</div>
    <?php elseif ($success === 2): ?>
      <div class="mt-4 p-3 rounded bg-red-50 border border-red-200 text-red-700">Gagal mengirim pesan. Coba lagi nanti.</div>
    <?php elseif ($error): ?>
      <div class="mt-4 p-3 rounded bg-red-50 border border-red-200 text-red-700"><?= $error ?></div>
    <?php endif; ?>
    <!-- end -->

    <!-- form contact -->
    <form id="contactForm" action="../app/process_contact.php" method="POST" class="mt-6 space-y-4 bg-white p-6 rounded-lg shadow">
      <div>
        <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
        <input id="nama" name="nama" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        <p class="mt-1 text-xs text-gray-400">Masukkan nama lengkap Anda.</p>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input id="email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-indigo-300" />
      </div>

      <div>
        <label for="pesan" class="block text-sm font-medium text-gray-700">Pesan</label>
        <textarea id="pesan" name="pesan" rows="6" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Kirim</button>
        <button type="reset" class="text-sm text-gray-600">Reset</button>
      </div>
    </form>
  </div>
  <!-- end form contact -->

  <!-- Sidebar: map, info kontak, social (ganti aside lama dengan ini) -->
<aside class="bg-white p-6 rounded-lg shadow space-y-6 w-full lg:w-[450px] xl:w-[520px]">
  <h3 class="font-semibold text-gray-800">Info Kontak</h3>

  <p class="text-sm text-gray-600">
    Email: <a href="mailto:admin@kampus.example" class="text-indigo-600 hover:underline">admin@kampus.example</a><br>
    Telp: <a href="tel:+6221123456" class="text-indigo-600 hover:underline">(021) 123-456</a>
  </p>

  <div>
    <h4 class="font-medium text-gray-700 mb-2">Alamat</h4>
    <p id="alamatText" class="text-sm text-gray-500">Jl. Contoh No.1, Kota Contoh, Indonesia</p>

    <div class="mt-3 flex gap-2">
      <button
        id="copyAddressBtn"
        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50 shadow-sm"
        aria-label="Salin alamat ke clipboard">
        <!-- copy icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M16 1H4a2 2 0 00-2 2v12h2V3h12V1z"/><path d="M20 5H8a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2zm0 16H8V7h12v14z"/>
        </svg>
        Copy Alamat
      </button>

      <button
        id="openMapBtn"
        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 shadow"
        aria-haspopup="dialog" aria-controls="mapModal">
        <!-- expand icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M10 3H5a2 2 0 00-2 2v5h2V5h5V3zM14 21h5a2 2 0 002-2v-5h-2v5h-5v2zM21 7h-5V5h5v2zM3 17h5v2H3v-2z"/>
        </svg>
        Perbesar Peta
      </button>
    </div>
  </div>

  <!-- Thumbnail map (klik juga akan membuka modal) -->
  <div class="w-full h-40 overflow-hidden rounded-lg border border-gray-100 mt-3">
    <button id="mapThumbBtn" class="w-full h-full block p-0 m-0" aria-label="Buka peta besar" style="all: unset;">
      <iframe
        title="Peta lokasi kampus (thumbnail)"
        width="200%"
        height="200%"
        frameborder="0"
        style="border:0"
        src="https://www.google.com/maps?q=Jl.+Contoh+No.1+Kota+Contoh+Indonesia&output=embed"
        allowfullscreen
        loading="lazy">
      </iframe>
    </button>
  </div>

  <!-- Social links -->
  <div>
  <h4 class="font-medium text-gray-700 mb-3">Ikuti Kami</h4>

  <div class="flex items-center gap-4">
    <!-- Instagram -->
    <a href="https://instagram.com/yourhandle" target="_blank" rel="noopener noreferrer"
       class="w-10 h-10 flex items-center justify-center rounded-full shadow hover:scale-110 transition-transform"
       aria-label="Instagram">
      <img src="https://cdn-icons-png.flaticon.com/128/174/174855.png" alt="Instagram" class="w-5 h-5" />
    </a>

    <!-- WhatsApp -->
    <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer"
       class="w-10 h-10 flex items-center justify-center rounded-full shadow hover:scale-110 transition-transform"
       aria-label="WhatsApp">
      <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp" class="w-5 h-5" />
    </a>

    <!-- Twitter / X -->
    <a href="https://twitter.com/yourhandle" target="_blank" rel="noopener noreferrer"
       class="w-10 h-10 flex items-center justify-center rounded-full shadow hover:scale-110 transition-transform"
       aria-label="Twitter">
      <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter" class="w-5 h-5" />
    </a>

    <!-- Facebook -->
    <a href="https://facebook.com/yourpage" target="_blank" rel="noopener noreferrer"
       class="w-10 h-10 flex items-center justify-center rounded-full shadow hover:scale-110 transition-transform"
       aria-label="Facebook">
      <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook" class="w-5 h-5" />
    </a>
  </div>
</div>
</aside>

<!-- Modal untuk peta full-screen -->
<div id="mapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="bg-white rounded-lg overflow-hidden w-full max-w-5xl h-[80vh] shadow-lg">
    <div class="flex items-center justify-between p-3 border-b">
      <h3 class="text-lg font-medium text-gray-800">Peta Lokasi</h3>
      <button id="closeMapBtn" class="text-gray-600 hover:text-gray-800" aria-label="Tutup peta">
        ✕
      </button>
    </div>
    <div class="w-full h-full">
      <iframe
        id="mapIframe"
        width="100%"
        height="100%"
        frameborder="0"
        style="border:0"
        src="https://www.google.com/maps?q=Jl.+Contoh+No.1+Kota+Contoh+Indonesia&output=embed"
        allowfullscreen>
      </iframe>
    </div>
  </div>
</div>

<!-- Script: copy alamat + buka modal -->
<script>
  // Copy alamat ke clipboard
  document.getElementById('copyAddressBtn').addEventListener('click', function() {
    const text = document.getElementById('alamatText').innerText.trim();
    if (!navigator.clipboard) {
      // fallback
      const el = document.createElement('textarea');
      el.value = text;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
      alert('Alamat disalin ke clipboard');
      return;
    }
    navigator.clipboard.writeText(text).then(() => {
      // notifikasi kecil (bisa diganti toast)
      this.classList.add('ring-2', 'ring-green-300');
      setTimeout(() => this.classList.remove('ring-2', 'ring-green-300'), 800);
    }, () => {
      alert('Gagal menyalin alamat');
    });
  });

  // Modal map
  const mapModal = document.getElementById('mapModal');
  const openMapBtn = document.getElementById('openMapBtn');
  const mapThumbBtn = document.getElementById('mapThumbBtn');
  const closeMapBtn = document.getElementById('closeMapBtn');

  function openModal() {
    mapModal.classList.remove('hidden');
    mapModal.classList.add('flex');
    mapModal.setAttribute('aria-hidden', 'false');
  }
  function closeModal() {
    mapModal.classList.add('hidden');
    mapModal.classList.remove('flex');
    mapModal.setAttribute('aria-hidden', 'true');
  }

  openMapBtn.addEventListener('click', openModal);
  mapThumbBtn.addEventListener('click', openModal);
  closeMapBtn.addEventListener('click', closeModal);
  // close on backdrop click
  mapModal.addEventListener('click', (e) => {
    if (e.target === mapModal) closeModal();
  });
</script>

<!-- Simple client-side validation (enhanced UX) -->
<script>
document.getElementById('contactForm').addEventListener('submit', function(e){
  const form = e.target;
  if (!form.reportValidity()) {
    e.preventDefault();
  }
});
</script>

<?php include __DIR__ . '/_footer.php'; ?>
