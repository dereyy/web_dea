<?php
// app/functions.php
if (!function_exists('e')) {
  function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}

if (!function_exists('slugify')) {
  function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
  }
}

// Generate CSRF token dan simpan di session
if (!function_exists('csrf_token')) {
  function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (empty($_SESSION['_csrf_token'])) {
      $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
  }
}

// Validasi CSRF token dari POST
if (!function_exists('verify_csrf')) {
  function verify_csrf() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (empty($_POST['_csrf_token']) || empty($_SESSION['_csrf_token'])) {
      return false;
    }
    return hash_equals($_SESSION['_csrf_token'], $_POST['_csrf_token']);
  }
}



