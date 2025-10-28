<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../app/functions.php';

$logged = isset($_SESSION['user_id']);
$current = basename($_SERVER['PHP_SELF']);

function dashboard_link_for_role()
{
  $role = $_SESSION['user_role'] ?? 'user';
  // jika ada admin role dan file admin dashboard tersedia, kembalikan link admin
  $adminFile = __DIR__ . '/admin/admin_dashboard.php';
  if ($role === 'admin' && file_exists($adminFile)) {
    return 'admin/admin_dashboard.php';
  }
  return 'users/users_dashboard.php';
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Portofolio Dea</title>

  <!-- Roboto font -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root {
      --page-max-width: 1100px;
      /* reduce width to make header smaller */
    }

    html,
    body {
      font-family: 'Roboto', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    }

    .page-container {
      max-width: var(--page-max-width);
      margin-left: auto;
      margin-right: auto;
    }

    /* small animation for dropdown */
    .dropdown-enter {
      transform-origin: top right;
    }

    /* fixed header + content spacing */
    .site-header {
      position: fixed;
      top: 16px;
      left: 0;
      right: 0;
      z-index: 50;
      pointer-events: auto;
    }

    /* ensure content is not hidden behind fixed header */
    .site-content {
      padding-top: 120px;
      /* adjust if header height changes */
    }

    /* stronger shadow on all sides */
    .pill-shadow {
      box-shadow: 0 10px 30px rgba(2, 6, 23, 0.12), 0 2px 8px rgba(2, 6, 23, 0.06) !important;
    }
  </style>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-b from-white/60 via-sky-50 to-white text-gray-800">

  <!-- Header: white pill container with centered nav like design example -->
  <header class="site-header">
    <div class="page-container mx-auto px-4">
      <div class="bg-white rounded-full pill-shadow px-6 py-3 flex items-center justify-between">
        <!-- Logo -->
        <a href="index.php#hero" class="flex items-center gap-3">
          <span class="text-lg font-semibold text-gray-800 ml-3">Web Dea</span>
        </a>

        <!-- Right cluster: nav + login -->
        <div class="flex items-center gap-2">
          <nav>
            <ul class="flex items-center gap-4 text-sm">
              <!-- Use a neutral default class; JS will toggle active state after DOM is ready -->
              <li><a href="index.php#hero" class="nav-link text-gray-600 hover:text-purple-600">Home</a></li>
              <li><a href="index.php#portfolio" class="nav-link text-gray-600 hover:text-purple-600">Portfolio</a></li>
              <li><a href="index.php#about" class="nav-link text-gray-600 hover:text-purple-600">About me</a></li>
            </ul>
          </nav>

          <div>
            <?php if (!$logged): ?>
              <a href="login.php" class="inline-flex items-center px-4 py-2 rounded-full border-2 border-purple-500 text-purple-600 hover:bg-purple-50">Login</a>
            <?php else: ?>
              <div class="relative" id="profileDropdownRoot">
                <button id="profileBtn" aria-expanded="false" aria-haspopup="true" class="flex items-center gap-3 px-3 py-1 rounded-full focus:outline-none">
                  <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? 'User') ?>&background=ffffff&color=6b21a8&rounded=true" alt="avatar" class="w-8 h-8 rounded-full border" />
                </button>
                <div id="profileDropdown" class="hidden dropdown-enter absolute right-0 mt-2 w-44 bg-white text-gray-800 rounded-md shadow-lg ring-1 ring-black/10 z-50 overflow-hidden">
                  <a href="<?= dashboard_link_for_role() ?>" class="block px-4 py-2 text-sm hover:bg-gray-100">Dashboard</a>
                  <form action="logout.php" method="POST" class="m-0">
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                  </form>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main class="site-content flex-grow page-container px-4 py-8">

    <script>
      // Dropdown logic: toggle + close on outside click + ESC
      (function() {
        const btn = document.getElementById('profileBtn');
        const dd = document.getElementById('profileDropdown');
        if (!btn || !dd) return;

        function openDropdown() {
          dd.classList.remove('hidden');
          btn.setAttribute('aria-expanded', 'true');
        }

        function closeDropdown() {
          dd.classList.add('hidden');
          btn.setAttribute('aria-expanded', 'false');
        }
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          if (dd.classList.contains('hidden')) openDropdown();
          else closeDropdown();
        });

        // click outside
        document.addEventListener('click', (e) => {
          if (!dd.classList.contains('hidden')) {
            if (!dd.contains(e.target) && !btn.contains(e.target)) closeDropdown();
          }
        });

        // esc
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') closeDropdown();
        });
      })();

      // Active nav on scroll: highlight current section link
      // Wait for DOMContentLoaded so sections (hero, portfolio, etc.) exist
      document.addEventListener('DOMContentLoaded', function() {
        const ids = ['hero', 'portfolio', 'about'];
        const links = Array.from(document.querySelectorAll('.nav-link'));
        if (!links.length) return;

        // enable smooth scrolling behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // If the page loaded with a hash (e.g. index.php#about), set that link active
        function setActiveByHash() {
          const hash = window.location.hash || '';
          if (!hash) return;
          links.forEach(a => {
            const href = a.getAttribute('href') || '';
            if (href.endsWith(hash)) {
              a.classList.add('text-purple-600', 'font-semibold');
              a.classList.remove('text-gray-600');
            } else {
              a.classList.remove('text-purple-600', 'font-semibold');
              a.classList.add('text-gray-600');
            }
          });
        }

        // IntersectionObserver for scroll-based active state
        const observerOpts = {
          root: null,
          rootMargin: '0px',
          threshold: 0.45
        };
        const io = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const id = entry.target.id;
              links.forEach(a => {
                const href = a.getAttribute('href') || '';
                if (href.endsWith('#' + id)) {
                  a.classList.add('text-purple-600', 'font-semibold');
                  a.classList.remove('text-gray-600');
                } else {
                  a.classList.remove('text-purple-600', 'font-semibold');
                  a.classList.add('text-gray-600');
                }
              });
            }
          });
        }, observerOpts);

        ids.forEach(id => {
          const el = document.getElementById(id);
          if (el) io.observe(el);
        });

        // initialize from hash (if any)
        setActiveByHash();
        // update active state when hash changes (e.g., via browser history/navigation)
        window.addEventListener('hashchange', setActiveByHash);
      });
    </script>