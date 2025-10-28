<?php
// deprecated: redirect to new admin delete endpoint
header('Location: ../portofolio/delete.php?id=' . urlencode($_POST['id'] ?? $_GET['id'] ?? ''), true, 302);
exit;
