<?php
// public/admin/_header.php
// Pastikan session & helper sudah tersedia
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../app/functions.php'; // path ke app
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin — Kampus Epic</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 text-gray-800">
  <div class="flex min-h-screen">
    <!-- Sidebar akan di-include terpisah -->
