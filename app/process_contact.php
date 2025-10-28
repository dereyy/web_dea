<?php
// app/process_contact.php — disabled because contact feature removed
if (session_status() === PHP_SESSION_NONE) session_start();
header('Location: /public/index.php');
exit;
