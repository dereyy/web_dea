<?php
// app/process_comment.php — dinonaktifkan karena fitur komentar dihapus
if (session_status() === PHP_SESSION_NONE) session_start();
header('Location: /index.php');
exit;
