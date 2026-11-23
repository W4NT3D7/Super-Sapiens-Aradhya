<?php
require_once __DIR__ . '/config.php';

$current_user = get_logged_in_user($pdo);
$view_mode = clean_input($_GET['feed'] ?? ($current_user ? 'personalized' : 'all'));

$user_sports = [];
if ($current_user && !empty($current_user['favorite_sports'])) {
    $user_sports = array_map('trim', explode(',', $current_user['favorite_sports']));
}

// Fetch all news items linked with athletes and contracts
try {
    $sql = "
        SELECT n.*, a.full_name as athlete_name, a.avatar_icon, a.sport_discipline, a.age_category, c.ticker, c.current_price
        FROM news_feed n
        LEFT JOIN athletes a ON n.athlete_id = a.id
        LEFT JOIN athlete_contracts c ON a.id = c.athlete_id
        ORDER BY n.published_at DESC
    ";
    $stmt = $pdo->query($sql);
    $all_news = $stmt->fetchAll();
} catch (Exception $e) {
    $all_news = [];
}

// Personalized News Algorithm Ranking
$filtered_news = [];
foreach ($all_news as $item) {
    $is_fav = in_array($item['sport_discipline'], $user_sports);

    if ($view_mode === 'personalized') {
        // Algorithm: prioritize user's favorite sports, then general
        if ($is_fav || empty($user_sports)) {
            $filtered_news[] = array_merge($item, ['algo_match' => true]);
        }
    } elseif ($view_mode === 'olympic') {
        if (in_array($item['sport_discipline'], ['Athletics', 'Swimming', 'Boxing', 'Wrestling', 'Archery', 'Badminton'])) {
            $filtered_news[] = $item;
        }
    } elseif ($view_mode === 'indigenous') {
        if (in_array($item['sport_discipline'], ['Kalaripayattu', 'Mallakhamba', 'Gatka', 'Thang-Ta', 'Yogasana'])) {
            $filtered_news[] = $item;
        }
    } else {
        $filtered_news[] = $item;
    }
}

// If personalized was selected but had zero exact matches, fallback gracefully to all news
if (empty($filtered_news)) {
    $filtered_news = $all_news;
}

$page_title = 'Personalized News & Updates Algorithm Feed | Super Sapiens';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-10 sm:py-14 flex-1">
  <div class="container mx-auto px-4 sm:px-6 max-w-5xl space-y-8">

    <!-- Top Headline Section (Requirement 5) -->
    <div class="text-center max-w-3xl mx-auto">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-3">
        Requirement 5: News & Updates Algorithm
      </div>
      <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
        Personalized Athlete News Feed
      </h2>
      <p class="text-slate-400 text-xs sm:text-sm mt-2">
        Real-time tournament updates, state trial heats, and KISCE scouting announcements. Filtered by your preferred sports.
      </p>
    </div>

    <!-- Algorithm Feed Switcher Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-2 rounded-2xl bg-slate-950/80 border border-slate-800">
      <div class="flex flex-wrap items-center gap-2">
        <a href="news.php?feed=personalized"
          class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?php echo ($view_mode === 'personalized') ? 'bg-orange-500 text-white shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
          <span>✨</span> For You (Algorithm)
        </a>
        <a href="news.php?feed=all"
          class="px-4 py-2 rounded-xl text-xs font-bold transition <?php echo ($view_mode === 'all') ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
          All Sports News
        </a>
        <a href="news.php?feed=olympic"
          class="px-4 py-2 rounded-xl text-xs font-bold transition <?php echo ($view_mode === 'olympic') ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
          🏃 Olympic Disciplines
        </a>
        <a href="news.php?feed=indigenous"
          class="px-4 py-2 rounded-xl text-xs font-bold transition <?php echo ($view_mode === 'indigenous') ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
          🚩 Indigenous Arts (Kalaripayattu, Gatka, Mallakhamba)
        </a>
      </div>

      <?php if ($current_user): ?>
        <a href="profile.php" class="text-[11px] text-slate-400 hover:text-orange-400 font-medium px-2">
          Adjust Sports Interests (<?php echo count($user_sports); ?> followed) ⚙️
        </a>
      <?php endif; ?>
    </div>

    <!-- Feed Items List -->
    <div class="space-y-6 reveal-group">
      <?php foreach ($filtered_news as $item): 
        $impact = (float)$item['impact_pct'];
        $is_bullish = ($item['sentiment'] === 'BULLISH');
      ?>
        <article class="reveal reveal-pop premium-card bg-slate-950/80 rounded-3xl border border-slate-800 p-6 sm:p-8 hover:border-slate-700 transition shadow-xl space-y-4 relative overflow-hidden group">
          
          <!-- Athlete Header & Sentiment Badge -->
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <span class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-xl">
                <?php echo $item['avatar_icon'] ?: '🏃'; ?>
              </span>
              <div>
                <a href="athlete_profile.php?id=<?php echo $item['athlete_id']; ?>" class="text-sm font-bold text-white hover:text-orange-400 transition">
                  <?php echo htmlspecialchars($item['athlete_name']); ?>
                </a>
                <span class="text-xs text-slate-400 block">
                  <?php echo htmlspecialchars($item['sport_discipline']); ?> • <?php echo htmlspecialchars($item['age_category']); ?>
                </span>
              </div>
            </div>

            <!-- Sentiment & Market Impact -->
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider <?php echo $is_bullish ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-300'; ?>">
                <?php echo $is_bullish ? '🟢 ' . $item['sentiment'] : '⚪ ' . $item['sentiment']; ?>
              </span>
              <span class="px-2.5 py-1 rounded-xl text-[10px] font-mono font-black <?php echo ($impact >= 0) ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'; ?>">
                Impact: <?php echo ($impact >= 0 ? '+' : '') . number_format($impact, 1); ?>%
              </span>
            </div>
          </div>

          <!-- News Content -->
          <div>
            <h3 class="text-lg sm:text-xl font-black text-white group-hover:text-orange-400 transition">
              <?php echo htmlspecialchars($item['title']); ?>
            </h3>
            <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
              <?php echo htmlspecialchars($item['summary']); ?>
            </p>
          </div>

          <!-- Footer Bar: Ticker Link, Date, Category -->
          <div class="pt-4 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3 text-slate-400 text-[11px]">
              <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 uppercase font-bold text-slate-400 text-[10px]">
                <?php echo htmlspecialchars($item['category']); ?>
              </span>
              <span>Published: <?php echo date('d M Y, h:i A', strtotime($item['published_at'])); ?></span>
            </div>

            <?php if (!empty($item['ticker'])): ?>
              <div class="flex items-center gap-2">
                <span class="font-mono text-[11px] text-slate-400">Linked Contract:</span>
                <a href="trading.php?ticker=<?php echo urlencode($item['ticker']); ?>" class="px-3 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 hover:text-slate-950 text-emerald-400 font-mono font-bold text-xs transition flex items-center gap-1">
                  <span><?php echo htmlspecialchars($item['ticker']); ?> (₹<?php echo number_format($item['current_price'], 2); ?>)</span>
                  <span>→</span>
                </a>
              </div>
            <?php endif; ?>
          </div>

        </article>
      <?php endforeach; ?>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

