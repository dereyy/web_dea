<?php
// deprecated: redirect to new admin edit page
header('Location: ../portofolio/edit.php?id=' . urlencode($_GET['id'] ?? ''), true, 302);
exit;
