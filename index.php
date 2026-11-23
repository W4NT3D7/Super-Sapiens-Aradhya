<?php
require_once __DIR__ . '/config.php';
$sports = get_sports_disciplines();
$current_user = get_logged_in_user($pdo);

// Fetch top trending contracts for the homepage ticker and featured showcase
try {
    $contracts = $pdo->query("
        SELECT c.*, a.full_name as athlete_name, a.avatar_icon, a.sport_discipline, a.state, a.talent_score,
               ((c.current_price - c.previous_close) / c.previous_close * 100) as pct_change
        FROM athlete_contracts c
        JOIN athletes a ON c.athlete_id = a.id
        ORDER BY c.volume_contracts DESC
        LIMIT 4
    ")->fetchAll();
} catch (Exception $e) {
    $contracts = [];
}

$page_title = 'Super Sapiens • Grassroots Sports & Athlete Contract Exchange';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Notice & Live Ticker -->
<div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-950 text-white py-2 px-4 border-b border-indigo-900/50 text-xs sm:text-sm">
  <div class="container mx-auto flex items-center justify-between gap-4">
    <div class="flex items-center gap-2 overflow-hidden">
      <span class="bg-orange-500 text-white uppercase text-[10px] font-extrabold px-2 py-0.5 rounded tracking-wide shrink-0">Official Alert</span>
      <p class="truncate text-slate-300">
        Athlete Onboarding & Talent Contract Trading for the <strong>8th Khelo India Youth Games (KIYG 2026 Telangana)</strong> is now LIVE on Super Sapiens.
      </p>
    </div>
    <div class="flex items-center gap-3 shrink-0">
      <a href="discover.php" class="text-orange-400 font-bold hover:underline hidden sm:inline">Explore Athletes →</a>
      <a href="trading.php" class="text-emerald-400 font-bold hover:underline hidden sm:inline">F&O Desk →</a>
    </div>
  </div>
</div>

<!-- Live Trading Ticker Ribbon -->
<div class="bg-slate-950 border-b border-slate-800 py-2 overflow-x-auto text-xs font-mono">
  <div class="container mx-auto px-4 flex items-center gap-6 whitespace-nowrap">
    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest shrink-0">🔥 SUPER SAPIENS F&O TICKER:</span>
    <?php foreach ($contracts as $c): 
      $pct = (float)$c['pct_change'];
    ?>
      <a href="trading.php?ticker=<?php echo urlencode($c['ticker']); ?>" class="flex items-center gap-2 hover:opacity-80 transition shrink-0">
        <span class="text-white font-bold"><?php echo htmlspecialchars($c['ticker']); ?></span>
        <span class="text-slate-300">₹<?php echo number_format($c['current_price'], 2); ?></span>
        <span class="text-[11px] font-bold <?php echo ($pct >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
          <?php echo ($pct >= 0 ? '+' : '') . number_format($pct, 2); ?>%
        </span>
      </a>
      <span class="text-slate-700">•</span>
    <?php endforeach; ?>
  </div>
</div>

<!-- Hero Section -->
<section class="relative bg-sports-mesh text-white py-16 sm:py-24 overflow-hidden">
  <div class="absolute -top-32 -left-32 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl pointer-events-none animate-ambient-glow"></div>
  <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none animate-ambient-glow" style="animation-delay: 3s;"></div>

  <div class="container mx-auto px-4 sm:px-6 relative z-10">
    <div class="max-w-4xl mx-auto text-center">
      
      <!-- Ministry Badge -->
      <div class="reveal reveal-pop inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-orange-400 text-xs sm:text-sm font-semibold mb-6 shadow-lg shadow-orange-500/10">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        Ministry of Youth Affairs and Sports, Government of India
      </div>

      <h1 class="reveal stagger-1 text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight sm:leading-none mb-6">
        Reviving Grassroots Sports.<br>
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-orange-400 via-amber-300 to-emerald-400">
          Building India’s Olympic Champions.
        </span>
      </h1>

      <p class="reveal stagger-2 text-base sm:text-xl text-slate-300 font-normal leading-relaxed max-w-3xl mx-auto mb-10">
        <strong>Super Sapiens</strong> powers grassroots athletic discovery and national onboarding for the <strong>Khelo India School Games (KISG)</strong>. 
        Scouting top talent for the <span class="text-amber-400 font-bold">Government of India's ₹5 Lakh annual scholarship grant (8 years)</span> 
        while letting supporters track performance milestones and trade athlete talent contracts on the exchange.
      </p>

      <!-- CTA Buttons -->
      <div class="reveal stagger-3 flex flex-col sm:flex-row items-center justify-center gap-4 mb-14">
        <a href="register.php" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-base rounded-2xl shadow-xl shadow-orange-500/30 transition transform hover:-translate-y-1">
          <span>Register Athlete / Talent (Free)</span>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
        <a href="discover.php" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/40 text-emerald-300 font-bold text-base rounded-2xl transition transform hover:-translate-y-1">
          <span>🔍 Discover Athletes & F&O Desk</span>
        </a>
      </div>

      <!-- Live Countdown Ticker to KIYG 2026 Telangana -->
      <div id="countdown-wrapper" class="reveal reveal-pop stagger-4 premium-card glass-panel-dark rounded-3xl p-6 max-w-2xl mx-auto border border-white/10 shadow-2xl">
        <div class="flex items-center justify-center gap-2 text-xs uppercase tracking-wider font-extrabold text-amber-400 mb-4">
          <span class="inline-block w-2.5 h-2.5 rounded-full bg-orange-500"></span>
          8th Khelo India Youth Games (KIYG 2026) • Telangana Countdown
        </div>
        <div class="grid grid-cols-4 gap-3 sm:gap-6 text-center">
          <div class="bg-white/5 rounded-2xl p-3 border border-white/5">
            <span id="count-days" class="block text-2xl sm:text-4xl font-black text-white">--</span>
            <span class="text-[10px] sm:text-xs text-slate-400 uppercase font-semibold">Days</span>
          </div>
          <div class="bg-white/5 rounded-2xl p-3 border border-white/5">
            <span id="count-hours" class="block text-2xl sm:text-4xl font-black text-white">--</span>
            <span class="text-[10px] sm:text-xs text-slate-400 uppercase font-semibold">Hours</span>
          </div>
          <div class="bg-white/5 rounded-2xl p-3 border border-white/5">
            <span id="count-minutes" class="block text-2xl sm:text-4xl font-black text-white">--</span>
            <span class="text-[10px] sm:text-xs text-slate-400 uppercase font-semibold">Minutes</span>
          </div>
          <div class="bg-white/5 rounded-2xl p-3 border border-white/5">
            <span id="count-seconds" class="block text-2xl sm:text-4xl font-black text-orange-400">--</span>
            <span class="text-[10px] sm:text-xs text-slate-400 uppercase font-semibold">Seconds</span>
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-4 text-center">
          Dec 11–22, 2026 • 6 Telangana Host Cities • 27 Competitive & 2 Demonstration Sports
        </p>
      </div>

    </div>
  </div>
</section>

<!-- Key Ecosystem Highlights Grid -->
<section class="relative -mt-8 z-20">
  <div class="container mx-auto px-4 sm:px-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 reveal-group">
      
      <div class="reveal reveal-pop premium-card bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between">
        <div>
          <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center font-black text-xl mb-4">
            ₹5L
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-white">₹5 Lakh/Yr</h2>
          <p class="text-xs sm:text-sm font-semibold text-orange-400 uppercase tracking-wide mt-1">Govt Scholarship Grant</p>
        </div>
        <p class="text-xs text-slate-400 mt-3">Official Central Government grant (MYAS & SAI) disbursed for 8 consecutive years (Total ₹40 Lakh).</p>
      </div>

      <div class="reveal reveal-pop premium-card-emerald bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between">
        <div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-xl mb-4">
            F&O
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-white">Super Sapiens</h2>
          <p class="text-xs sm:text-sm font-semibold text-emerald-400 uppercase tracking-wide mt-1">Contract Trading</p>
        </div>
        <p class="text-xs text-slate-400 mt-3">Trade futures contracts tied to grassroots athlete tournament outcomes and podium finishes.</p>
      </div>

      <div class="reveal reveal-pop premium-card-blue bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between">
        <div>
          <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-black text-xl mb-4">
            29
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-white">29 Disciplines</h2>
          <p class="text-xs sm:text-sm font-semibold text-blue-400 uppercase tracking-wide mt-1">Olympic & Indigenous</p>
        </div>
        <p class="text-xs text-slate-400 mt-3">27 competitive sports (Softball debut) & 2 demo sports (Karra Samu, Sailing) in Telangana.</p>
      </div>

      <div class="reveal reveal-pop premium-card bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between">
        <div>
          <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center font-black text-xl mb-4">
            ₹10k
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-white">In-App Wallet</h2>
          <p class="text-xs sm:text-sm font-semibold text-purple-400 uppercase tracking-wide mt-1">₹10,000 Demo Credits</p>
        </div>
        <p class="text-xs text-slate-400 mt-3">Every newly registered user gets ₹10k in virtual simulation credits with instant UPI top-ups.</p>
      </div>

    </div>
  </div>
</section>

<!-- Section 1: Evolution & History -->
<section id="about" class="py-20 sm:py-28 bg-slate-900 border-b border-slate-800 relative overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 relative z-10">
    
    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 mb-3">
        1. Evolution & History
      </span>
      <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
        From School Rings to the Olympic Podium
      </h2>
      <p class="text-slate-400 text-base sm:text-lg mt-3">
        A structured sports ecosystem instituted by the Ministry of Youth Affairs and Sports to revive Indian sports culture at the root.
      </p>
    </div>

    <!-- Timeline cards with Staggered Emergence -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 reveal-group">
      
      <div class="reveal reveal-pop premium-card p-6 rounded-3xl bg-slate-950/80 border border-slate-800">
        <div class="flex items-center justify-between mb-4">
          <span class="text-2xl font-black text-orange-500">2018</span>
          <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-orange-500/20 text-orange-400">Launch</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Inaugural Khelo India School Games (New Delhi)</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Held from January 31 to February 8, 2018, in New Delhi. Featured exclusively Under-17 school athletes across 16 competitive sporting disciplines.
        </p>
      </div>

      <div class="reveal reveal-pop premium-card-blue p-6 rounded-3xl bg-slate-950/80 border border-slate-800">
        <div class="flex items-center justify-between mb-4">
          <span class="text-2xl font-black text-blue-500">2019</span>
          <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-500/20 text-blue-400">Expansion</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Rebranding to Khelo India Youth Games (Pune)</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          With Indian Olympic Association (IOA) partnership, expanded into KIYG. Introduced dual categories: Under-17 (school) and Under-21 (college/youth).
        </p>
      </div>

      <div class="reveal reveal-pop premium-card-emerald p-6 rounded-3xl bg-slate-950/80 border border-slate-800">
        <div class="flex items-center justify-between mb-4">
          <span class="text-2xl font-black text-emerald-500">Feeder</span>
          <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Revival</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Dedicated School Rounds (Ages 10–17)</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Structured dedicated district, state, and national school rounds acting as a direct feeder system into the flagship national Games and NSRS talent repository.
        </p>
      </div>

      <div class="reveal reveal-pop premium-card p-6 rounded-3xl bg-gradient-to-br from-orange-500/10 via-amber-500/10 to-transparent border-2 border-orange-500">
        <div class="flex items-center justify-between mb-4">
          <span class="text-2xl font-black text-orange-400">2026</span>
          <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-orange-500 text-white">Next Edition</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">8th KIYG 2026 Telangana (Dec 11–22)</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Hosted across 6 Telangana cities (Hyderabad, Khammam, Karimnagar, Hanumakonda, Mahabubnagar, Nizamabad) featuring 27 competitive & 2 demonstration sports.
        </p>
      </div>

    </div>

  </div>
</section>

<!-- Section 2: Key Objectives & Scholarships -->
<section id="scholarships" class="py-20 sm:py-28 bg-slate-950 text-white relative border-b border-slate-800 overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 relative z-10">
    
    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/20 text-orange-400 border border-orange-500/30 mb-3">
        2. Official Government of India (MYAS) Scheme
      </span>
      <h2 class="text-3xl sm:text-4xl font-black tracking-tight">
        Central Govt Scholarship: ₹5 Lakh / Year for 8 Years
      </h2>
      <p class="text-slate-400 text-base sm:text-lg mt-3">
        100% funded and disbursed directly by the Ministry of Youth Affairs and Sports (MYAS), Government of India, to ensure no scouted young talent drops out due to financial constraints.
      </p>

      <!-- Clear Govt Disbursement Notice -->
      <div class="reveal reveal-pop mt-6 inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs text-left max-w-2xl mx-auto shadow-lg shadow-amber-500/5">
        <span class="text-2xl shrink-0">🏛️</span>
        <div>
          <span class="font-extrabold uppercase tracking-wide block text-amber-400 text-[11px] mb-0.5">Government Disbursement Notice</span>
          <span>The ₹5 Lakh/year financial assistance is an official Central Government scholarship disbursed directly through the Khelo India Scheme / Sports Authority of India (SAI) via Direct Benefit Transfer (DBT). <strong>Super Sapiens</strong> operates the grassroots scouting engine, talent directory, and community exchange.</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 reveal-group">
      
      <div class="reveal reveal-pop premium-card p-6 rounded-3xl bg-slate-900/80 border border-slate-800">
        <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center font-bold text-xl mb-4">
          🎯
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Talent Identification</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Scouts promising athletes from school tournaments including National School Games (SGFI), CBSE Nationals, and State board meets.
        </p>
      </div>

      <div class="reveal reveal-pop premium-card p-6 rounded-3xl bg-slate-900/80 border border-slate-800">
        <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xl mb-4">
          💰
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Central Govt Financial Grant</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Top 1,000 scouted athletes receive ₹5 lakh annually for 8 years (Total ₹40 Lakhs from MYAS/SAI) for coaching, high-protein diet, equipment, and international exposure.
        </p>
      </div>

      <div class="reveal reveal-pop premium-card-emerald p-6 rounded-3xl bg-slate-900/80 border border-slate-800">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xl mb-4">
          🏛️
        </div>
        <h3 class="text-lg font-bold text-white mb-2">KISCE & Academies</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Trained at accredited Khelo India Academies and Khelo India State Centres of Excellence (KISCE) with sports science, physio, and recovery gear.
        </p>
      </div>

      <div class="reveal reveal-pop premium-card-blue p-6 rounded-3xl bg-slate-900/80 border border-slate-800">
        <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xl mb-4">
          🎖️
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Direct Career Incentives</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Medal winners at Khelo India games are officially eligible for direct recruitment and employment in Central Government departments and PSU sports quotas.
        </p>
      </div>

    </div>

  </div>
</section>

<!-- Section 3: Sports Disciplines (Olympic, Indigenous, & Telangana 2026 Additions) -->
<section id="disciplines" class="py-20 sm:py-28 bg-slate-900 text-white relative border-b border-slate-800 overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 relative z-10">

    <div class="text-center max-w-3xl mx-auto mb-14 reveal">
      <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 mb-3">
        3. Comprehensive Sports Disciplines
      </span>
      <h2 class="text-3xl sm:text-5xl font-black tracking-tight">
        29+ Sporting Arenas of Excellence
      </h2>
      <p class="text-slate-400 text-base sm:text-lg mt-3">
        Covering Olympic international sports alongside traditional Indian indigenous martial arts and Telangana debut sports.
      </p>
    </div>

    <!-- Category Filter Chips -->
    <div class="flex flex-wrap justify-center gap-2 mb-10 reveal">
      <button class="filter-chip active px-4 py-2 rounded-xl text-xs font-extrabold transition" data-filter="all">
        ⚡ All 29+ Disciplines
      </button>
      <button class="filter-chip px-4 py-2 rounded-xl text-xs font-extrabold transition" data-filter="olympic">
        🏅 Olympic & Major Sports (19)
      </button>
      <button class="filter-chip px-4 py-2 rounded-xl text-xs font-extrabold transition" data-filter="indigenous">
        🚩 Indigenous Heritage Sports (5)
      </button>
      <button class="filter-chip px-4 py-2 rounded-xl text-xs font-extrabold transition" data-filter="telangana">
        🔥 Telangana 2026 Additions (3)
      </button>
    </div>

    <!-- Sports Discipline Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 reveal-group mb-12">
      <?php
      $olympic_sports = [
        ['Athletics', '🏃', 'olympic'],
        ['Archery', '🏹', 'olympic'],
        ['Badminton', '🏸', 'olympic'],
        ['Basketball', '🏀', 'olympic'],
        ['Boxing', '🥊', 'olympic'],
        ['Cycling', '🚴', 'olympic'],
        ['Football', '⚽', 'olympic'],
        ['Gymnastics', '🤸', 'olympic'],
        ['Hockey', '🏑', 'olympic'],
        ['Judo', '🥋', 'olympic'],
        ['Kabaddi', '🤼', 'olympic'],
        ['Kho-Kho', '⚡', 'olympic'],
        ['Shooting', '🎯', 'olympic'],
        ['Swimming', '🏊', 'olympic'],
        ['Table Tennis', '🏓', 'olympic'],
        ['Tennis', '🎾', 'olympic'],
        ['Volleyball', '🏐', 'olympic'],
        ['Weightlifting', '🏋️', 'olympic'],
        ['Wrestling', '🤼‍♂️', 'olympic'],
        ['Mallakhamba', '🚩', 'indigenous'],
        ['Kalaripayattu', '⚔️', 'indigenous'],
        ['Gatka', '🛡️', 'indigenous'],
        ['Thang-Ta', '🗡️', 'indigenous'],
        ['Yogasana', '🧘', 'indigenous'],
        ['Softball (Debut)', '⚾', 'telangana'],
        ['Karra Samu (Demo)', '🥋', 'telangana'],
        ['Sailing (Demo)', '⛵', 'telangana']
      ];

      foreach ($olympic_sports as $sp):
        $name = $sp[0];
        $icon = $sp[1];
        $cat = $sp[2];
        $cat_badge = ($cat === 'indigenous') ? 'text-amber-400 bg-amber-500/10' : (($cat === 'telangana') ? 'text-orange-400 bg-orange-500/10' : 'text-slate-400 bg-slate-800/50');
      ?>
        <div class="sport-item-card reveal reveal-pop premium-card p-4 rounded-2xl bg-slate-950/80 border border-slate-800 flex flex-col items-center text-center justify-between" data-category="<?php echo $cat; ?>">
          <div class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center text-2xl mb-2.5 shadow-md">
            <?php echo $icon; ?>
          </div>
          <span class="text-xs font-bold text-white line-clamp-1 mb-1"><?php echo htmlspecialchars($name); ?></span>
          <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded <?php echo $cat_badge; ?>">
            <?php echo ucfirst($cat); ?>
          </span>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Indigenous Heritage Banner Spotlight -->
    <div class="reveal reveal-pop premium-card bg-gradient-to-r from-amber-950/40 via-slate-900 to-orange-950/40 rounded-3xl p-6 sm:p-8 border border-amber-500/30 shadow-2xl">
      <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
            Heritage Revival Initiative (2020 Onward)
          </div>
          <h3 class="text-xl sm:text-2xl font-black text-white">Traditional Indian Indigenous Disciplines</h3>
          <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
            Preserving cultural martial arts and gymnastic traditions: <strong>Mallakhamba</strong> (pole and rope acrobatic balance), 
            <strong>Kalaripayattu</strong> (ancient Kerala martial art), <strong>Gatka</strong> (Punjab stick-fighting art), 
            <strong>Thang-Ta</strong> (Manipur sword and spear discipline), and competitive <strong>Yogasana</strong>.
          </p>
        </div>
        <a href="register.php?category=indigenous" class="shrink-0 px-6 py-3.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-xs rounded-xl shadow-lg transition">
          Register for Indigenous Sports →
        </a>
      </div>
    </div>

  </div>
</section>

<!-- Section 4: Latest Edition Context (KIYG 2026 Telangana) -->
<section id="telangana-edition" class="py-20 sm:py-28 bg-slate-950 text-white relative border-b border-slate-800 overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 relative z-10">

    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
        4. Latest Edition Context
      </span>
      <h2 class="text-3xl sm:text-5xl font-black tracking-tight">
        8th Khelo India Youth Games (KIYG 2026)
      </h2>
      <p class="text-slate-400 text-base sm:text-lg mt-3">
        Hosted by <strong>Telangana</strong> from <strong>December 11 to 22, 2026</strong>, across six vibrant sports hub cities.
      </p>
    </div>

    <!-- 6 Telangana Host Cities Cards -->
    <div class="mb-14">
      <h4 class="text-xs uppercase tracking-widest font-black text-slate-400 text-center mb-6 reveal">
        6 Telangana Host Cities
      </h4>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 reveal-group">
        <?php
        $cities = [
          ['Hyderabad', 'GMC Balayogi Athletics & Aquatic Stadiums'],
          ['Khammam', 'Sardar Patel Regional Sports Complex'],
          ['Karimnagar', 'Ambedkar Indoor Stadium & Archery Range'],
          ['Hanumakonda', 'Jawaharlal Nehru Multi-Sport Arena'],
          ['Mahabubnagar', 'District Sports Authority Complex'],
          ['Nizamabad', 'Collectorate Sports Arena & Boxing Ring']
        ];
        foreach ($cities as $ct):
        ?>
          <div class="reveal reveal-pop premium-card p-5 rounded-2xl bg-slate-900 border border-slate-800 text-center">
            <span class="text-xl mb-1.5 block">🏛️</span>
            <h5 class="text-sm font-black text-white"><?php echo htmlspecialchars($ct[0]); ?></h5>
            <p class="text-[11px] text-slate-400 mt-1"><?php echo htmlspecialchars($ct[1]); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Debut & Demonstration Highlights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 reveal-group">
      
      <div class="reveal reveal-pop premium-card p-6 rounded-3xl bg-slate-900 border border-slate-800">
        <span class="text-2xl mb-3 block">⚾</span>
        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 mb-2 inline-block">Debutant Sport</span>
        <h4 class="text-lg font-bold text-white mb-2">Softball Competitive Debut</h4>
        <p class="text-xs text-slate-400 leading-relaxed">
          Softball makes its official historic competitive medal debut at the 8th Khelo India Youth Games in Telangana, expanding team ball sports.
        </p>
      </div>

      <div class="reveal reveal-pop premium-card p-6 rounded-3xl bg-slate-900 border border-slate-800">
        <span class="text-2xl mb-3 block">⛵</span>
        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 mb-2 inline-block">Demonstration</span>
        <h4 class="text-lg font-bold text-white mb-2">Sailing Regatta</h4>
        <p class="text-xs text-slate-400 leading-relaxed">
          Showcasing water sports precision on Hussain Sagar Lake in Hyderabad as an official demonstration discipline for scholastic contenders.
        </p>
      </div>

      <div class="reveal reveal-pop premium-card p-6 rounded-3xl bg-slate-900 border border-slate-800">
        <span class="text-2xl mb-3 block">🥋</span>
        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-orange-500/20 text-orange-400 mb-2 inline-block">Traditional Martial Art</span>
        <h4 class="text-lg font-bold text-white mb-2">Karra Samu Demonstration</h4>
        <p class="text-xs text-slate-400 leading-relaxed">
          Ancient Andhra/Telangana stick fighting martial art featured to revive regional indigenous sporting heritage before national audiences.
        </p>
      </div>

    </div>

  </div>
</section>

<!-- Section 5: Interactive 15-Second Eligibility Checker Quiz -->
<section id="eligibility" class="py-20 sm:py-28 bg-slate-900 border-b border-slate-800 relative overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 relative z-10">

    <div class="max-w-3xl mx-auto text-center mb-12 reveal">
      <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 mb-3">
        5. Eligibility & NSRS Feeder Check
      </span>
      <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
        Check Your Athlete Onboarding Eligibility
      </h2>
      <p class="text-slate-400 text-sm sm:text-base mt-2">
        Takes under 15 seconds. Find out your age bracket, school quota division, and qualifying trial pathway.
      </p>
    </div>

    <div class="reveal reveal-pop premium-card bg-slate-950 border border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl max-w-3xl mx-auto">
      <form id="eligibility-quiz-form" class="space-y-6">
        
        <!-- Question 1: Age Bracket -->
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">
            1. What is your age bracket as of December 2026?
          </label>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="age_bracket" value="u14" class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">Under-14 (10–13 yrs)</span>
            </label>
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="age_bracket" value="u17" checked class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">Under-17 School</span>
            </label>
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="age_bracket" value="u21" class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">Under-21 Youth</span>
            </label>
          </div>
        </div>

        <!-- Question 2: School Enrollment -->
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">
            2. Are you enrolled in a recognized Indian school or institution?
          </label>
          <div class="grid grid-cols-2 gap-3">
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="is_enrolled" value="yes" checked class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">Yes (CBSE, ICSE, State Board)</span>
            </label>
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="is_enrolled" value="no" class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">Open Feeder / Club</span>
            </label>
          </div>
        </div>

        <!-- Question 3: Competition Level -->
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">
            3. Highest competitive tournament level achieved:
          </label>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="comp_level" value="district" class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">District / Zonal Level</span>
            </label>
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="comp_level" value="state" checked class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">State Meet Medalist</span>
            </label>
            <label class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center gap-3 cursor-pointer hover:border-orange-500/50 transition">
              <input type="radio" name="comp_level" value="national" class="text-orange-500 focus:ring-orange-500">
              <span class="text-xs font-bold text-white">SGFI / CBSE Nationals</span>
            </label>
          </div>
        </div>

        <button type="submit" class="w-full py-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-sm rounded-xl shadow-xl shadow-orange-500/25 transition">
          Check My Eligibility & View Pathway →
        </button>
      </form>

      <!-- Instant Dynamic Result Box -->
      <div id="eligibility-result" class="mt-6 hidden"></div>
    </div>

  </div>
</section>

<!-- Section 6: Featured Athlete Contracts & F&O Market Preview -->
<section id="trading-preview" class="py-20 sm:py-28 bg-slate-950 text-white relative border-b border-slate-800 overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 relative z-10">

    <div class="text-center max-w-3xl mx-auto mb-16 reveal">
      <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
        6. Contract-Based Trading Ecosystem
      </span>
      <h2 class="text-3xl sm:text-5xl font-black tracking-tight">
        Trade Futures Linked to Emerging Talent
      </h2>
      <p class="text-slate-400 text-base sm:text-lg mt-3">
        Similar to Futures & Options (F&O). Back rising athletes ahead of tournament rounds, track price depth, and manage your portfolio.
      </p>
    </div>

    <!-- Contracts Grid with Emergence -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 reveal-group mb-12">
      <?php foreach ($contracts as $c): 
        $pct = (float)$c['pct_change'];
      ?>
        <div class="reveal reveal-pop premium-card bg-slate-900 rounded-3xl p-6 border border-slate-800 flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-2">
                <span class="text-2xl"><?php echo htmlspecialchars($c['avatar_icon'] ?? '🏃'); ?></span>
                <div>
                  <h4 class="text-sm font-black text-white"><?php echo htmlspecialchars($c['athlete_name']); ?></h4>
                  <span class="text-[10px] text-slate-400 font-semibold"><?php echo htmlspecialchars($c['sport_discipline']); ?></span>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-slate-300">
                Score <?php echo number_format($c['talent_score'], 1); ?>
              </span>
            </div>

            <div class="p-3 rounded-2xl bg-slate-950 border border-slate-850 mb-4">
              <span class="text-[10px] uppercase font-bold text-slate-500 block">Contract Ticker</span>
              <span class="text-xs font-black text-orange-400 font-mono"><?php echo htmlspecialchars($c['ticker']); ?></span>
              <p class="text-[11px] text-slate-400 mt-1 line-clamp-1"><?php echo htmlspecialchars($c['contract_title']); ?></p>
            </div>

            <div class="flex items-baseline justify-between mb-4">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Current Price</span>
                <span class="text-xl font-black text-white font-mono">₹<?php echo number_format($c['current_price'], 2); ?></span>
              </div>
              <span class="text-xs font-black px-2 py-1 rounded-lg <?php echo ($pct >= 0) ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'; ?>">
                <?php echo ($pct >= 0 ? '+' : '') . number_format($pct, 2); ?>%
              </span>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-800 flex items-center gap-2">
            <a href="trading.php?ticker=<?php echo urlencode($c['ticker']); ?>" class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-xs rounded-xl text-center transition">
              Trade Contract →
            </a>
            <a href="athlete_profile.php?id=<?php echo $c['athlete_id']; ?>" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition" title="View Dossier">
              👁️
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center reveal">
      <a href="trading.php" class="inline-flex items-center gap-2 px-8 py-4 bg-slate-900 hover:bg-slate-850 border border-slate-700 text-white font-extrabold text-sm rounded-2xl transition">
        <span>Open Super Sapiens F&O Terminal</span>
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </a>
    </div>

  </div>
</section>

<!-- Call To Action Section -->
<section class="py-20 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-600 text-white text-center relative overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 max-w-3xl relative z-10">
    <div class="reveal reveal-pop premium-card bg-black/20 backdrop-blur-xl rounded-3xl p-8 sm:p-12 border border-white/20 shadow-2xl">
      <h2 class="text-3xl sm:text-5xl font-black mb-4 tracking-tight">
        Ready to Discover India’s Sporting Future?
      </h2>
      <p class="text-orange-100 text-base sm:text-lg mb-8 leading-relaxed">
        Whether you are an aspiring school athlete registering for national scouting trials or a sports supporter trading talent contracts, begin your Super Sapiens journey today.
      </p>
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="register.php" class="w-full sm:w-auto px-8 py-4 bg-white text-orange-600 hover:bg-slate-100 font-black text-base rounded-2xl shadow-xl transition transform hover:-translate-y-1">
          Register Athlete (KISG 2026) →
        </a>
        <a href="discover.php" class="w-full sm:w-auto px-8 py-4 bg-slate-950/80 hover:bg-slate-950 text-white font-bold text-base rounded-2xl border border-white/20 transition transform hover:-translate-y-1">
          Explore Talent Discovery & F&O Desk
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
