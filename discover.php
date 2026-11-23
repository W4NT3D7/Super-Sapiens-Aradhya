<?php
require_once __DIR__ . '/config.php';

$search_q = clean_input($_GET['q'] ?? '');
$category_filter = clean_input($_GET['cat'] ?? 'all');
$sport_filter = clean_input($_GET['sport'] ?? '');

// Build High-Speed Discovery Query
$where_clauses = [];
$params = [];

if (!empty($search_q)) {
    $where_clauses[] = "(a.full_name LIKE :q1 OR a.sport_discipline LIKE :q2 OR a.sub_event LIKE :q3 OR a.state LIKE :q4 OR c.ticker LIKE :q5)";
    $params[':q1'] = "%$search_q%";
    $params[':q2'] = "%$search_q%";
    $params[':q3'] = "%$search_q%";
    $params[':q4'] = "%$search_q%";
    $params[':q5'] = "%$search_q%";
}

if ($category_filter === 'indigenous') {
    $where_clauses[] = "a.sport_discipline IN ('Mallakhamba', 'Kalaripayattu', 'Gatka', 'Thang-Ta', 'Yogasana')";
} elseif ($category_filter === 'olympic') {
    $where_clauses[] = "a.sport_discipline IN ('Athletics', 'Archery', 'Badminton', 'Boxing', 'Swimming', 'Wrestling', 'Shooting', 'Football', 'Hockey', 'Gymnastics', 'Weightlifting', 'Table Tennis')";
} elseif ($category_filter === 'telangana') {
    $where_clauses[] = "a.state = 'Telangana'";
}

if (!empty($sport_filter)) {
    $where_clauses[] = "a.sport_discipline = :sp";
    $params[':sp'] = $sport_filter;
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

try {
    $sql = "
        SELECT a.*, c.ticker, c.current_price, c.previous_close, c.high_24h, c.low_24h, c.volume_contracts, c.open_interest,
               ((c.current_price - c.previous_close) / c.previous_close * 100) as pct_change
        FROM athletes a
        LEFT JOIN athlete_contracts c ON a.id = c.athlete_id
        {$where_sql}
        ORDER BY a.talent_score DESC, c.volume_contracts DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $athletes = $stmt->fetchAll();
} catch (Exception $e) {
    $athletes = [];
    $error = $e->getMessage();
}

$page_title = 'Athlete & Talent Discovery Platform | Super Sapiens';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-10 sm:py-14 flex-1">
  <div class="container mx-auto px-4 sm:px-6 max-w-7xl space-y-8">

    <!-- Top Headline Section -->
    <div class="text-center max-w-3xl mx-auto">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 mb-3">
        Requirements 3 & 4: Talent Discovery & Search
      </div>
      <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
        Discover India’s Emerging Athletes
      </h2>
      <p class="text-slate-400 text-sm sm:text-base mt-2">
        Explore scouted school & youth prodigies across Olympic and Indigenous sports. Track their tournament milestones, talent ratings, and linked contracts.
      </p>
    </div>

    <!-- Live Search & Filter Bar -->
    <div class="bg-slate-950/80 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-5">
      
      <!-- Search Input -->
      <form action="discover.php" method="GET" class="relative">
        <input type="text" name="q" value="<?php echo htmlspecialchars($search_q); ?>" placeholder="Search by athlete name, sport, state, school, or contract ticker (e.g. Aarav, Kalaripayattu, Punjab, MEERA-SWIM)..."
          class="w-full pl-11 pr-32 py-3.5 bg-slate-900 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 font-semibold shadow-inner">
        <svg class="w-5 h-5 text-slate-500 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <button type="submit" class="absolute right-2 top-2 px-5 py-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
          Search
        </button>
      </form>

      <!-- Category Filter Pills -->
      <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-800/80">
        <div class="flex flex-wrap items-center gap-2">
          <a href="discover.php?cat=all<?php echo !empty($search_q) ? '&q=' . urlencode($search_q) : ''; ?>"
            class="px-4 py-1.5 rounded-xl text-xs font-bold transition <?php echo ($category_filter === 'all') ? 'bg-orange-500 text-white shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
            All Athletes
          </a>
          <a href="discover.php?cat=olympic<?php echo !empty($search_q) ? '&q=' . urlencode($search_q) : ''; ?>"
            class="px-4 py-1.5 rounded-xl text-xs font-bold transition <?php echo ($category_filter === 'olympic') ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
            🏃 Olympic Sports
          </a>
          <a href="discover.php?cat=indigenous<?php echo !empty($search_q) ? '&q=' . urlencode($search_q) : ''; ?>"
            class="px-4 py-1.5 rounded-xl text-xs font-bold transition <?php echo ($category_filter === 'indigenous') ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
            🚩 Indigenous Heritage (Kalaripayattu, Gatka, Mallakhamba)
          </a>
          <a href="discover.php?cat=telangana<?php echo !empty($search_q) ? '&q=' . urlencode($search_q) : ''; ?>"
            class="px-4 py-1.5 rounded-xl text-xs font-bold transition <?php echo ($category_filter === 'telangana') ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'; ?>">
            🏛️ Host State (Telangana 2026)
          </a>
        </div>

        <span class="text-xs text-slate-500 font-medium">
          Showing <strong><?php echo count($athletes); ?></strong> Scouted Athletes
        </span>
      </div>

    </div>

    <!-- Athletes Grid -->
    <?php if (empty($athletes)): ?>
      <div class="p-12 text-center bg-slate-950/60 rounded-3xl border border-slate-800">
        <span class="text-4xl block mb-3">🔍</span>
        <h3 class="text-lg font-bold text-white mb-1">No athletes found matching your search</h3>
        <p class="text-xs text-slate-400 mb-4">Try searching with a broader keyword, or explore our top Olympic & Indigenous categories.</p>
        <a href="discover.php" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition">
          Reset Search Filters
        </a>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($athletes as $ath): 
          $pct = (float)($ath['pct_change'] ?? 0);
          $price = (float)($ath['current_price'] ?? 100);
        ?>
          <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-6 hover:border-slate-700 transition flex flex-col justify-between hover-lift shadow-xl relative overflow-hidden group">
            
            <!-- Top Athlete Header -->
            <div>
              <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                  <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 flex items-center justify-center text-3xl shadow-inner shrink-0 group-hover:scale-105 transition">
                    <?php echo $ath['avatar_icon'] ?: '🏃'; ?>
                  </div>
                  <div>
                    <h3 class="text-lg font-black text-white group-hover:text-orange-400 transition">
                      <a href="athlete_profile.php?id=<?php echo $ath['id']; ?>">
                        <?php echo htmlspecialchars($ath['full_name']); ?>
                      </a>
                    </h3>
                    <span class="text-xs text-slate-400 font-semibold block">
                      <?php echo htmlspecialchars($ath['sport_discipline']); ?> • <?php echo htmlspecialchars($ath['age_category']); ?>
                    </span>
                  </div>
                </div>

                <!-- Talent Score Badge -->
                <div class="text-right shrink-0">
                  <span class="text-[9px] uppercase font-bold text-slate-400 block">Talent Score</span>
                  <span class="text-sm font-black text-amber-400 font-mono bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                    ★ <?php echo number_format($ath['talent_score'], 1); ?>
                  </span>
                </div>
              </div>

              <!-- Location & School -->
              <p class="text-xs text-slate-300 font-medium mb-3 truncate">
                🏫 <?php echo htmlspecialchars($ath['school_name']); ?> (<?php echo htmlspecialchars($ath['district']); ?>, <?php echo htmlspecialchars($ath['state']); ?>)
              </p>

              <!-- Qualification Highlights -->
              <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800/80 text-xs mb-4">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Qualifying Merit:</span>
                <span class="font-bold text-blue-400"><?php echo htmlspecialchars($ath['qualification_level']); ?></span>
                <p class="text-[11px] text-slate-400 mt-1 line-clamp-2">
                  <?php echo htmlspecialchars($ath['achievement_details']); ?>
                </p>
              </div>

              <!-- Contract / Trading Market Card (Requirement 6: F&O Contract Link) -->
              <?php if (!empty($ath['ticker'])): ?>
                <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between mb-4">
                  <div>
                    <span class="text-[9px] uppercase font-bold text-slate-500 block font-mono">F&O CONTRACT</span>
                    <span class="text-xs font-black text-white font-mono"><?php echo htmlspecialchars($ath['ticker']); ?></span>
                  </div>
                  <div class="text-right">
                    <span class="text-base font-black text-white font-mono">₹<?php echo number_format($price, 2); ?></span>
                    <span class="text-[10px] font-bold block <?php echo ($pct >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
                      <?php echo ($pct >= 0 ? '+' : '') . number_format($pct, 2); ?>% (24h)
                    </span>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-800/80 flex items-center gap-2">
              <a href="athlete_profile.php?id=<?php echo $ath['id']; ?>" class="flex-1 text-center py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                View Dossier & Track →
              </a>
              <?php if (!empty($ath['ticker'])): ?>
                <a href="trading.php?ticker=<?php echo urlencode($ath['ticker']); ?>" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-xs transition shadow flex items-center gap-1">
                  <span>Trade</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
              <?php endif; ?>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

