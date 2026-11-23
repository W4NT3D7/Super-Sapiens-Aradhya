<?php
require_once __DIR__ . '/../config.php';
$current_user = get_logged_in_user($pdo);
$page_title = $page_title ?? 'Super Sapiens • Grassroots Athlete Discovery & Talent Exchange';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>

  <!-- Tailwind CSS CDN + Custom Sports & FinTech Theme -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased selection:bg-orange-500 selection:text-white min-h-screen flex flex-col">

  <!-- Ambient Scroll Progress Bar -->
  <div id="scroll-progress-bar"></div>

  <!-- Tricolor National Strip -->
  <div class="gov-strip"></div>

  <!-- Main Navigation Bar -->
  <header class="bg-slate-950/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-800">
    <div class="container mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
      
      <!-- Brand & Title -->
      <a href="index.php" class="flex items-center gap-3 shrink-0">
        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 via-amber-500 to-emerald-500 text-white font-black text-lg shadow-md shadow-orange-500/20 tracking-tighter">
          SS
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="text-[9px] font-extrabold uppercase tracking-wider text-orange-400 bg-orange-500/10 px-1.5 py-0.5 rounded border border-orange-500/20">Grassroots Sports</span>
            <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20">Talent Exchange</span>
          </div>
          <h1 class="text-base font-black text-white tracking-tight leading-none mt-0.5">
            SUPER <span class="text-orange-500">SAPIENS</span>
          </h1>
        </div>
      </a>

      <!-- Global Search Bar (Requirement 4: Search Functionality) -->
      <div class="hidden md:flex flex-1 max-w-xs lg:max-w-md mx-2">
        <form action="discover.php" method="GET" class="w-full relative">
          <input type="text" name="q" placeholder="Search athletes, sports, ticker (e.g. Aarav, 100m, Kalaripayattu)..." 
            value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
            class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
          <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </form>
      </div>

      <!-- Desktop Navigation Links -->
      <nav class="hidden lg:flex items-center gap-6 text-xs font-bold text-slate-300">
        <a href="discover.php" class="hover:text-orange-400 transition flex items-center gap-1.5">
          <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
          Discover Athletes
        </a>
        <a href="trading.php" class="hover:text-emerald-400 transition flex items-center gap-1.5">
          <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
          F&O Trading Desk
        </a>
        <a href="news.php" class="hover:text-blue-400 transition flex items-center gap-1.5">
          <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
          News & Feed
        </a>
        <a href="register.php" class="text-orange-400 hover:text-orange-300 transition">
          Onboard Athlete (KISG)
        </a>
      </nav>

      <!-- User Account & Wallet / Profile Section (Requirements 2, 7) -->
      <div class="flex items-center gap-3">
        <?php if ($current_user): ?>
          <!-- Wallet Balance Pill -->
          <a href="wallet.php" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500/50 transition">
            <span class="text-[10px] uppercase font-bold text-slate-400">Wallet</span>
            <span class="text-xs font-black text-emerald-400 font-mono">₹<?php echo number_format($current_user['wallet_balance'], 2); ?></span>
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          </a>

          <!-- Profile Icon Dropdown -->
          <div class="relative group">
            <a href="profile.php" class="flex items-center gap-2 p-1 rounded-xl hover:bg-slate-800 transition">
              <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-amber-500 text-white font-extrabold text-xs flex items-center justify-center shadow-md">
                <?php echo strtoupper(substr($current_user['full_name'], 0, 2)); ?>
              </div>
              <span class="hidden md:inline text-xs font-bold text-slate-200"><?php echo htmlspecialchars($current_user['username']); ?></span>
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </a>

            <!-- Dropdown Menu -->
            <div class="absolute right-0 mt-1 w-52 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-2 hidden group-hover:block z-50">
              <div class="px-3 py-2 border-b border-slate-800 mb-1">
                <span class="text-xs font-black text-white block truncate"><?php echo htmlspecialchars($current_user['full_name']); ?></span>
                <span class="text-[10px] text-slate-400 block truncate"><?php echo htmlspecialchars($current_user['email']); ?></span>
              </div>
              <a href="profile.php" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg">
                👤 My Profile & Settings
              </a>
              <a href="wallet.php" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg">
                💳 In-App Wallet (₹<?php echo number_format($current_user['wallet_balance'], 2); ?>)
              </a>
              <a href="trading.php" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg">
                📊 Open F&O Positions
              </a>
              <a href="admin/index.php" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg">
                🏛️ Federation Admin
              </a>
              <div class="pt-1 border-t border-slate-800 mt-1">
                <a href="user_logout.php" class="flex items-center gap-2 px-3 py-2 text-xs font-bold text-red-400 hover:bg-red-500/10 rounded-lg">
                  🚪 Sign Out
                </a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <!-- Logged Out View -->
          <a href="user_login.php" class="text-xs font-bold text-slate-300 hover:text-white px-3 py-2">
            Sign In
          </a>
          <a href="user_register.php" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-orange-500/20 transition">
            Register / Join
          </a>
        <?php endif; ?>

        <!-- Mobile Menu Toggle Button -->
        <button id="mobile-menu-btn" class="lg:hidden p-2 text-slate-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
      </div>

    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-800 bg-slate-950 px-4 py-4 space-y-3">
      <form action="discover.php" method="GET" class="w-full relative mb-3">
        <input type="text" name="q" placeholder="Search athletes..." 
          class="w-full pl-9 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
        <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </form>
      <a href="discover.php" class="block text-slate-300 font-semibold text-sm hover:text-orange-400">Discover Athletes</a>
      <a href="trading.php" class="block text-slate-300 font-semibold text-sm hover:text-emerald-400">F&O Trading Desk</a>
      <a href="news.php" class="block text-slate-300 font-semibold text-sm hover:text-blue-400">News & Feed Algorithm</a>
      <a href="register.php" class="block text-orange-400 font-semibold text-sm">Onboard Athlete (KISG 2026)</a>
      <a href="wallet.php" class="block text-emerald-400 font-semibold text-sm">In-App Wallet</a>
      <?php if ($current_user): ?>
        <a href="profile.php" class="block text-slate-300 font-semibold text-sm">My Profile</a>
        <a href="user_logout.php" class="block text-red-400 font-semibold text-sm">Sign Out</a>
      <?php else: ?>
        <div class="pt-2 border-t border-slate-800 flex gap-2">
          <a href="user_login.php" class="w-1/2 text-center py-2 text-xs font-bold text-slate-300 border border-slate-800 rounded-xl">Sign In</a>
          <a href="user_register.php" class="w-1/2 text-center py-2 text-xs font-bold text-white bg-orange-500 rounded-xl">Register</a>
        </div>
      <?php endif; ?>
    </div>
  </header>

