<?php
// app/process_contact.php
include __DIR__ . '/../config/config.php';

// jika kita request interaksi pada contact
// POST untuk tambah data atau add new data
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../public/contact.php');
    exit;
}

// kolom nama, email dan pesan
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');

// kalau misal ada salah satu dari data nya yang kosong
if ($nama === '' || $email === '' || $pesan === '') {
    // dia akan bilang semua field wajib diiisi
    header('Location: ../public/contact.php?error=' . urlencode('Semua field wajib diisi'));
    exit;
}

// basic sanitize (you already use prepared statements, but keep safe)
$nama = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
// string@gmail.sting nama@string.com
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$pesan = htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8');

// logika untuk menambahkan data
$stmt = mysqli_prepare($conn, "INSERT INTO contacts (nama, email, pesan) VALUES (?, ?, ?)");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $pesan);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // jika kita melakukan input data tersebut akan masuk dan berhasil
    if ($ok) {
        header('Location: ../public/contact.php?success=1');
        exit;
    } else {
        header('Location: ../public/contact.php?success=2');
        exit;
    }
} else {
    header('Location: ../public/contact.php?success=2');
    exit;
}

// jika sudah selesai input maka dia akan selesai
mysqli_close($conn);
