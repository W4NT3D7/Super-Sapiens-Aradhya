<?php
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 1);

try {
    $stmt = $pdo->prepare("
        SELECT a.*, c.id as contract_id, c.ticker, c.current_price, c.previous_close, c.high_24h, c.low_24h, c.volume_contracts, c.open_interest, c.lot_size,
               ((c.current_price - c.previous_close) / c.previous_close * 100) as pct_change
        FROM athletes a
        LEFT JOIN athlete_contracts c ON a.id = c.athlete_id
        WHERE a.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $athlete = $stmt->fetch();

    if (!$athlete) {
        header('Location: discover.php');
        exit;
    }

    // Fetch related news articles
    $stmt_news = $pdo->prepare("SELECT * FROM news_feed WHERE athlete_id = :aid ORDER BY id DESC LIMIT 5");
    $stmt_news->execute([':aid' => $id]);
    $news_items = $stmt_news->fetchAll();

} catch (Exception $e) {
    header('Location: discover.php');
    exit;
}

$pct = (float)($athlete['pct_change'] ?? 0);
$price = (float)($athlete['current_price'] ?? 100);
$page_title = $athlete['full_name'] . ' • Talent Dossier & F&O Market | Super Sapiens';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-10 sm:py-14 flex-1">
  <div class="container mx-auto px-4 sm:px-6 max-w-6xl space-y-8">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
      <a href="discover.php" class="hover:text-white transition">Discover Athletes</a>
      <span>/</span>
      <span><?php echo htmlspecialchars($athlete['sport_discipline']); ?></span>
      <span>/</span>
      <span class="text-white"><?php echo htmlspecialchars($athlete['full_name']); ?></span>
    </div>

    <!-- Athlete Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-700/80 shadow-2xl relative overflow-hidden">
      <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
        
        <div class="flex items-center gap-6">
          <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-slate-800 to-slate-950 border-2 border-orange-500/40 flex items-center justify-center text-5xl shadow-xl shrink-0">
            <?php echo $athlete['avatar_icon'] ?: '🏃'; ?>
          </div>

          <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
              <h2 class="text-3xl font-black text-white"><?php echo htmlspecialchars($athlete['full_name']); ?></h2>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                <?php echo htmlspecialchars($athlete['age_category']); ?>
              </span>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                ● <?php echo htmlspecialchars($athlete['status']); ?>
              </span>
            </div>

            <p class="text-sm text-slate-300 font-medium">
              <?php echo htmlspecialchars($athlete['sport_discipline']); ?> (<?php echo htmlspecialchars($athlete['sub_event']); ?>) • 
              <span class="text-slate-400"><?php echo htmlspecialchars($athlete['school_name']); ?></span>
            </p>

            <p class="text-xs text-slate-400 mt-1">
              Location: <?php echo htmlspecialchars($athlete['district']); ?>, <?php echo htmlspecialchars($athlete['state']); ?> • 
              Affiliation: <?php echo htmlspecialchars($athlete['board']); ?> • 
              National App ID: <span class="font-mono text-amber-400"><?php echo htmlspecialchars($athlete['application_id']); ?></span>
            </p>
          </div>
        </div>

        <!-- Live Talent Rating Index -->
        <div class="bg-slate-950/80 p-5 rounded-2xl border border-slate-800 text-right shrink-0">
          <span class="text-[10px] font-bold uppercase text-slate-400 block">Talent Scouting Index</span>
          <div class="text-3xl font-black text-amber-400 font-mono mt-0.5">
            ★ <?php echo number_format($athlete['talent_score'], 1); ?><span class="text-xs text-slate-500">/100</span>
          </div>
          <span class="text-[11px] text-emerald-400 font-semibold block mt-1">
            Top 0.5% Grassroots Feeder
          </span>
        </div>

      </div>
    </div>

    <!-- Main Content Layout (Left: Sporting Profile & News, Right: Trading Widget) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Left Column: Dossier, Records & News Feed (2 Columns wide) -->
      <div class="lg:col-span-2 space-y-8">
        
        <!-- Sporting Credentials Card -->
        <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6">
          <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
            <span>🏆</span> Sporting Qualifications & Track Record
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Highest Level Competed</span>
              <span class="text-sm font-bold text-blue-400 mt-0.5 block"><?php echo htmlspecialchars($athlete['qualification_level']); ?></span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Competitive Experience</span>
              <span class="text-sm font-bold text-white mt-0.5 block"><?php echo (int)$athlete['playing_experience']; ?> Years Active Training</span>
            </div>

            <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-900 border border-slate-800">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Tournament Achievements & Medals</span>
              <p class="text-slate-200 mt-1 leading-relaxed font-medium">
                "<?php echo nl2br(htmlspecialchars($athlete['achievement_details'])); ?>"
              </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Coach / Training Academy</span>
              <span class="text-xs font-bold text-slate-300 mt-0.5 block"><?php echo htmlspecialchars($athlete['coach_name'] ?? 'Self'); ?></span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Certificate / Licence</span>
              <span class="text-xs font-mono font-bold text-slate-300 mt-0.5 block"><?php echo htmlspecialchars($athlete['certificate_no'] ?? 'N/A'); ?></span>
            </div>
          </div>

          <!-- KISCE Trial & Venue -->
          <div class="p-4 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-xs">
            <span class="text-[10px] font-bold uppercase text-orange-400 block">Talent Scouting Assessment Center</span>
            <p class="text-white font-bold text-sm mt-0.5"><?php echo htmlspecialchars($athlete['trial_venue']); ?></p>
            <p class="text-slate-400 text-[11px] mt-1">
              Assessment Date: <strong><?php echo date('d F, Y', strtotime($athlete['trial_date'])); ?></strong>. 
              Targeted for induction into Khelo India State Centres of Excellence (KISCE) & official Government of India ₹5 Lakh annual scholarship grant.
            </p>
          </div>
        </div>

        <!-- Requirement 5: News & Updates Algorithm Feed for this athlete -->
        <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                <span>📰</span> News & Performance Milestones
              </h3>
              <p class="text-xs text-slate-400">Algorithmic feed tracking this athlete's journey</p>
            </div>
            <a href="news.php" class="text-xs font-bold text-orange-400 hover:text-orange-300">View All Sports News →</a>
          </div>

          <?php if (empty($news_items)): ?>
            <div class="p-6 text-center text-slate-500 text-xs">
              No recent news updates published for this athlete. Check back after upcoming trial heats!
            </div>
          <?php else: ?>
            <div class="space-y-4">
              <?php foreach ($news_items as $item): ?>
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-2 hover:border-slate-700 transition">
                  <div class="flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30">
                      <?php echo htmlspecialchars($item['category']); ?>
                    </span>
                    <span class="text-[10px] font-bold <?php echo ($item['impact_pct'] >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
                      Contract Impact: <?php echo ($item['impact_pct'] >= 0 ? '+' : '') . number_format($item['impact_pct'], 1); ?>%
                    </span>
                  </div>
                  <h4 class="text-sm font-black text-white"><?php echo htmlspecialchars($item['title']); ?></h4>
                  <p class="text-xs text-slate-400 leading-relaxed"><?php echo htmlspecialchars($item['summary']); ?></p>
                  <span class="text-[10px] text-slate-500 block">Published <?php echo date('d M Y, h:i A', strtotime($item['published_at'])); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

      </div>

      <!-- Right Column: F&O Contract & Trading Terminal (Requirement 6) -->
      <div class="space-y-6">
        
        <?php if (!empty($athlete['ticker'])): ?>
          <div class="bg-slate-950/90 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-2xl sticky top-24">
            
            <div class="border-b border-slate-800 pb-4">
              <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block font-mono">TALENT FUTURES CONTRACT</span>
              <h3 class="text-xl font-black text-white font-mono mt-0.5"><?php echo htmlspecialchars($athlete['ticker']); ?></h3>
              <p class="text-xs text-slate-400 mt-1">Expiry: Dec 2026 (KIYG Telangana Finals)</p>
            </div>

            <!-- Price & Change -->
            <div class="flex items-baseline justify-between">
              <div>
                <span class="text-3xl font-black text-white font-mono">₹<?php echo number_format($price, 2); ?></span>
                <span class="text-xs font-bold ml-2 <?php echo ($pct >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
                  <?php echo ($pct >= 0 ? '+' : '') . number_format($pct, 2); ?>% (24h)
                </span>
              </div>
            </div>

            <!-- 24h High, Low, Volume Stats -->
            <div class="grid grid-cols-2 gap-3 text-xs bg-slate-900/60 p-3 rounded-2xl border border-slate-800 font-mono">
              <div>
                <span class="text-[10px] text-slate-500 block">24h High</span>
                <span class="text-white font-bold">₹<?php echo number_format($athlete['high_24h'], 2); ?></span>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block">24h Low</span>
                <span class="text-white font-bold">₹<?php echo number_format($athlete['low_24h'], 2); ?></span>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block">Volume</span>
                <span class="text-white font-bold"><?php echo number_format($athlete['volume_contracts']); ?></span>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block">Open Interest</span>
                <span class="text-white font-bold"><?php echo number_format($athlete['open_interest']); ?></span>
              </div>
            </div>

            <!-- Quick Trade Action Button -->
            <div class="space-y-3">
              <a href="trading.php?ticker=<?php echo urlencode($athlete['ticker']); ?>" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-black text-sm rounded-2xl shadow-xl shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                <span>Open in F&O Trading Desk</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
              </a>
              <p class="text-[11px] text-slate-500 text-center">
                1 Lot = <?php echo (int)$athlete['lot_size']; ?> Contracts • Margin required: ₹<?php echo number_format($price * (int)$athlete['lot_size'], 2); ?>
              </p>
            </div>

            <!-- User Wallet Quick Balance -->
            <?php if ($current_user): ?>
              <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-400">Your Wallet Balance:</span>
                <span class="font-black text-emerald-400 font-mono">₹<?php echo number_format($current_user['wallet_balance'], 2); ?></span>
              </div>
            <?php endif; ?>

          </div>
        <?php endif; ?>

      </div>

    </div>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

