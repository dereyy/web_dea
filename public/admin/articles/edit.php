<?php
// public/admin/articles/edit.php
require_once __DIR__ . '/../../../app/auth.php';
require_admin();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../app/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

// ambil data
$stmt = mysqli_prepare($conn, "SELECT id, title, content, featured_image FROM articles WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$article = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);
if (!$article) { header('Location: index.php'); exit; }

$errors = [];
$title = $article['title'];
$content = $article['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');

  if ($title === '' || $content === '') $errors[] = 'Judul dan isi wajib diisi.';

  // handle image replace
  if (!empty($_FILES['featured_image']['name'])) {
    $allowed = ['image/jpeg','image/png','image/webp'];
    if (!in_array($_FILES['featured_image']['type'], $allowed)) {
      $errors[] = 'Tipe file tidak diperbolehkan.';
    } else {
      $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
      $fname = 'uploads/' . uniqid('img_') . '.' . $ext;
      $target = __DIR__ . '/../../../public/' . $fname;
      if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
        // hapus gambar lama jika ada
        if ($article['featured_image'] && file_exists(__DIR__ . '/../../../public/' . $article['featured_image'])) {
          @unlink(__DIR__ . '/../../../public/' . $article['featured_image']);
        }
        $article['featured_image'] = $fname;
      } else {
        $errors[] = 'Gagal upload gambar.';
      }
    }
  }

  if (empty($errors)) {
    $stmt = mysqli_prepare($conn, "UPDATE articles SET title = ?, content = ?, featured_image = ?, updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $title, $content, $article['featured_image'], $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if ($ok) {
      header('Location: index.php?updated=1');
      exit;
    } else {
      $errors[] = 'Gagal menyimpan perubahan.';
    }
  }
}

// include layout
include __DIR__ . '/../_header_admin.php';
include __DIR__ . '/../_sidebar_admin.php';
?>

<main class="flex-1 p-8">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Edit Artikel</h1>

    <?php if ($errors): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
        <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow space-y-4">
      <div><label class="block text-sm font-medium">Judul</label>
        <input name="title" value="<?= e($title) ?>" class="mt-1 block w-full border px-3 py-2 rounded" required></div>

      <div><label class="block text-sm font-medium">Isi</label>
        <textarea id="editor" name="content" rows="12"><?= e($content) ?></textarea></div>

      <div>
        <label class="block text-sm font-medium">Gambar Saat Ini</label>
        <?php if ($article['featured_image']): ?>
          <img src="<?= e('../../' . $article['featured_image']) ?>" class="w-48 h-32 object-cover rounded mb-2">
        <?php else: ?>
          <div class="w-48 h-32 bg-gray-100 rounded flex items-center justify-center text-sm text-gray-400 mb-2">No image</div>
        <?php endif; ?>
        <div><label class="block text-sm">Ganti Gambar (opsional)</label>
          <input type="file" name="featured_image" accept="image/*" class="mt-1"></div>
      </div>

      <div class="flex items-center gap-3">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded" type="submit">Simpan Perubahan</button>
        <a href="index.php" class="text-sm text-gray-600">Batal</a>
      </div>
    </form>
  </div>
</main>

<?php include __DIR__ . '/../_footer_admin.php'; ?>

<!-- TinyMCE init same as create.php -->
<script src="https://cdn.tiny.cloud/1/pveptn3rvibyvg0w1znpaddkzpnzut5pfy7bp4qlmyov14pl/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '#editor',
  height: 400,
  plugins: 'image link media table lists code',
  toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
  menubar: false
});
</script>
