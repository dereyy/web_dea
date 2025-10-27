<?php
// public/admin/articles/create.php
require_once __DIR__ . '/../../../app/auth.php';
require_admin();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../app/functions.php';

$errors = [];
$title = $content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');

  if ($title === '' || $content === '') {
    $errors[] = 'Judul dan isi artikel wajib diisi.';
  }

  // handle image upload (opsional)
  $imagePath = null;
  if (!empty($_FILES['featured_image']['name'])) {
    $allowed = ['image/jpeg','image/png','image/webp'];
    if (!in_array($_FILES['featured_image']['type'], $allowed)) {
      $errors[] = 'Tipe file tidak diperbolehkan. Gunakan JPG/PNG/WEBP.';
    } else {
      $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
      $fname = 'uploads/' . uniqid('img_') . '.' . $ext;
      $target = __DIR__ . '/../../../public/' . $fname;
      if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
        $errors[] = 'Gagal mengunggah gambar.';
      } else {
        $imagePath = $fname; // simpan relatif ke public/
      }
    }
  }

  if (empty($errors)) {
    $slug = slugify($title);
    // jika slug duplicate, tambahkan unique suffix
    $origSlug = $slug;
    $i = 1;
    while (true) {
      $stmt = mysqli_prepare($conn, "SELECT id FROM articles WHERE slug = ?");
      mysqli_stmt_bind_param($stmt, "s", $slug);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if (mysqli_stmt_num_rows($stmt) === 0) {
        mysqli_stmt_close($stmt);
        break;
      }
      mysqli_stmt_close($stmt);
      $slug = $origSlug . '-' . $i++;
    }

    $author_id = $_SESSION['user_id'] ?? null;
    $stmt = mysqli_prepare($conn, "INSERT INTO articles (title, slug, content, featured_image, author_id) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssi", $title, $slug, $content, $imagePath, $author_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
      header('Location: index.php?created=1');
      exit;
    } else {
      $errors[] = 'Gagal menyimpan artikel ke database.';
    }
  }
}

// include layout
include __DIR__ . '/../_header_admin.php';
include __DIR__ . '/../_sidebar_admin.php';
?>

<main class="flex-1 p-8">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Buat Artikel</h1>

    <?php if ($errors): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
          <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow space-y-4">
      <div>
        <label class="block text-sm font-medium">Judul</label>
        <input name="title" value="<?= e($title) ?>" class="mt-1 block w-full border px-3 py-2 rounded" required>
      </div>

      <div>
        <label class="block text-sm font-medium">Isi</label>
        <textarea id="editor" name="content" rows="12"><?= e($content) ?></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium">Gambar Utama (opsional)</label>
        <input type="file" name="featured_image" accept="image/*" class="mt-1">
      </div>

      <div class="flex items-center gap-3">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded" type="submit">Simpan</button>
        <a href="index.php" class="text-sm text-gray-600">Batal</a>
      </div>
    </form>
  </div>
</main>

<?php include __DIR__ . '/../_footer_admin.php'; ?>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/pveptn3rvibyvg0w1znpaddkzpnzut5pfy7bp4qlmyov14pl/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '#editor',
  height: 400,
  plugins: 'image link media table lists code',
  toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
  menubar: false
});
</script>
