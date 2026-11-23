<?php
require_once __DIR__ . '/config.php';
$sports = get_sports_disciplines();
$current_user = get_logged_in_user($pdo);

// Fetch top trending contracts for the homepage ticker
try {
    $contracts = $pdo->query("
        SELECT c.*, a.full_name as athlete_name, a.avatar_icon, a.sport_discipline,
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
  <div class="absolute -top-32 -left-32 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>

  <div class="container mx-auto px-4 sm:px-6 relative z-10">
    <div class="max-w-4xl mx-auto text-center">
      
      <!-- Ministry Badge -->
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-orange-400 text-xs sm:text-sm font-semibold mb-6">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        Ministry of Youth Affairs and Sports, Government of India
      </div>

      <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight sm:leading-none mb-6">
        Reviving Grassroots Sports.<br>
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-orange-400 via-amber-300 to-emerald-400">
          Building India’s Olympic Champions.
        </span>
      </h1>

      <p class="text-base sm:text-xl text-slate-300 font-normal leading-relaxed max-w-3xl mx-auto mb-10">
        <strong>Super Sapiens</strong> powers grassroots athletic discovery and national onboarding for the <strong>Khelo India School Games (KISG)</strong>. 
        Scouting top talent for the <span class="text-amber-400 font-bold">Government of India's ₹5 Lakh annual scholarship grant (8 years)</span> 
        while letting supporters track performance milestones and trade athlete talent contracts on the exchange.
      </p>

      <!-- CTA Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-14">
        <a href="register.php" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-base rounded-2xl shadow-xl shadow-orange-500/30 transition transform hover:-translate-y-0.5">
          <span>Register Athlete / Talent (Free)</span>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
        <a href="discover.php" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/40 text-emerald-300 font-bold text-base rounded-2xl transition">
          <span>🔍 Discover Athletes & F&O Desk</span>
        </a>
      </div>

      <!-- Live Countdown Ticker to KIYG 2026 Telangana -->
      <div id="countdown-wrapper" class="glass-panel-dark rounded-3xl p-6 max-w-2xl mx-auto border border-white/10 shadow-2xl">
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      
      <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between hover-lift">
        <div>
          <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center font-black text-xl mb-4">
            ₹5L
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-white">₹5 Lakh/Yr</h2>
          <p class="text-xs sm:text-sm font-semibold text-orange-400 uppercase tracking-wide mt-1">Govt Scholarship Grant</p>
        </div>
        <p class="text-xs text-slate-400 mt-3">Official Central Government grant (MYAS & SAI) disbursed for 8 consecutive years (Total ₹40 Lakh).</p>
      </div>

      <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between hover-lift">
        <div>
          <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-xl mb-4">
            F&O
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-white">Super Sapiens</h2>
          <p class="text-xs sm:text-sm font-semibold text-emerald-400 uppercase tracking-wide mt-1">Contract Trading</p>
        </div>
        <p class="text-xs text-slate-400 mt-3">Trade futures contracts tied to grassroots athlete tournament outcomes and podium finishes.</p>
      </div>

      <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between hover-lift">
        <div>
          <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-black text-xl mb-4">
            29
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-white">29 Disciplines</h2>
          <p class="text-xs sm:text-sm font-semibold text-blue-400 uppercase tracking-wide mt-1">Olympic & Indigenous</p>
        </div>
        <p class="text-xs text-slate-400 mt-3">27 competitive sports (Softball debut) & 2 demo sports (Karra Samu, Sailing) in Telangana.</p>
      </div>

      <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between hover-lift">
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
<section id="about" class="py-20 sm:py-28 bg-slate-900 border-b border-slate-800">
  <div class="container mx-auto px-4 sm:px-6">
    
    <div class="text-center max-w-3xl mx-auto mb-16">
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

    <!-- Timeline cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      
      <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 hover:border-orange-500/50 transition hover-lift">
        <div class="flex items-center justify-between mb-4">
          <span class="text-2xl font-black text-orange-500">2018</span>
          <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-orange-500/20 text-orange-400">Launch</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Inaugural Khelo India School Games (New Delhi)</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Held from January 31 to February 8, 2018, in New Delhi. Featured exclusively Under-17 school athletes across 16 competitive sporting disciplines.
        </p>
      </div>

      <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 hover:border-blue-500/50 transition hover-lift">
        <div class="flex items-center justify-between mb-4">
          <span class="text-2xl font-black text-blue-500">2019</span>
          <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-500/20 text-blue-400">Expansion</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Rebranding to Khelo India Youth Games (Pune)</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          With Indian Olympic Association (IOA) partnership, expanded into KIYG. Introduced dual categories: Under-17 (school) and Under-21 (college/youth).
        </p>
      </div>

      <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 hover:border-emerald-500/50 transition hover-lift">
        <div class="flex items-center justify-between mb-4">
          <span class="text-2xl font-black text-emerald-500">Feeder</span>
          <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Revival</span>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Dedicated School Rounds (Ages 10–17)</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Structured dedicated district, state, and national school rounds acting as a direct feeder system into the flagship national Games and NSRS talent repository.
        </p>
      </div>

      <div class="p-6 rounded-3xl bg-gradient-to-br from-orange-500/10 via-amber-500/10 to-transparent border-2 border-orange-500 hover-lift">
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
<section id="scholarships" class="py-20 sm:py-28 bg-slate-950 text-white relative border-b border-slate-800">
  <div class="container mx-auto px-4 sm:px-6 relative z-10">
    
    <div class="text-center max-w-3xl mx-auto mb-16">
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
      <div class="mt-6 inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs text-left max-w-2xl mx-auto">
        <span class="text-2xl shrink-0">🏛️</span>
        <div>
          <span class="font-extrabold uppercase tracking-wide block text-amber-400 text-[11px] mb-0.5">Government Disbursement Notice</span>
          <span>The ₹5 Lakh/year financial assistance is an official Central Government scholarship disbursed directly through the Khelo India Scheme / Sports Authority of India (SAI) via Direct Benefit Transfer (DBT). <strong>Super Sapiens</strong> operates the grassroots scouting engine, talent directory, and community exchange.</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-orange-500 transition">
        <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center font-bold text-xl mb-4">
          🎯
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Talent Identification</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Scouts promising athletes from school tournaments including National School Games (SGFI), CBSE Nationals, and State board meets.
        </p>
      </div>

      <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-amber-400 transition">
        <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xl mb-4">
          💰
        </div>
        <h3 class="text-lg font-bold text-white mb-2">Central Govt Financial Grant</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Top 1,000 scouted athletes receive ₹5 lakh annually for 8 years (Total ₹40 Lakhs from MYAS/SAI) for coaching, high-protein diet, equipment, and international exposure.
        </p>
      </div>

      <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-emerald-400 transition">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xl mb-4">
          🏛️
        </div>
        <h3 class="text-lg font-bold text-white mb-2">KISCE & Academies</h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Trained at accredited Khelo India Academies and Khelo India State Centres of Excellence (KISCE) with sports science, physio, and recovery gear.
        </p>
      </div>

      <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 hover:border-blue-400 transition">
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

<!-- Call To Action Section -->
<section class="py-16 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-600 text-white text-center">
  <div class="container mx-auto px-4 sm:px-6 max-w-3xl">
    <h2 class="text-3xl sm:text-4xl font-black mb-4">
      Ready to Discover India’s Sporting Future?
    </h2>
    <p class="text-orange-100 text-base sm:text-lg mb-8">
      Whether you are an aspiring athlete registering for trials or a supporter trading talent futures, start your journey today.
    </p>
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <a href="register.php" class="w-full sm:w-auto px-8 py-4 bg-white text-orange-600 font-black rounded-2xl shadow-xl hover:bg-slate-100 transition">
        Register Athlete (KISG 2026) →
      </a>
      <a href="discover.php" class="w-full sm:w-auto px-8 py-4 bg-orange-700/60 hover:bg-orange-700 text-white font-bold rounded-2xl border border-white/20 transition">
        Explore Talent Discovery & F&O Desk
      </a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
