<?php
require_once __DIR__ . '/config.php';

$app_id = clean_input($_GET['app_id'] ?? '');

$athlete = null;
if (!empty($app_id)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM athletes WHERE application_id = :id LIMIT 1");
        $stmt->execute([':id' => $app_id]);
        $athlete = $stmt->fetch();
    } catch (Exception $e) {
        $athlete = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Confirmation Pass | Super Sapiens</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased">

  <div class="gov-strip"></div>

  <!-- Header -->
  <header class="no-print bg-white border-b border-slate-200 py-3">
    <div class="container mx-auto px-4 sm:px-6 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-black flex items-center justify-center">SS</div>
        <span class="text-sm font-black text-slate-900 uppercase">Super Sapiens Portal</span>
      </a>
      <div class="flex items-center gap-2">
        <a href="status.php" class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-2">Check Application Status</a>
        <a href="index.php" class="text-xs font-bold text-orange-600 hover:text-orange-700 px-3 py-2">Home</a>
      </div>
    </div>
  </header>

  <main class="py-10 sm:py-16">
    <div class="container mx-auto px-4 sm:px-6 max-w-3xl">

      <!-- Success Notification Box -->
      <div class="no-print mb-8 p-6 rounded-3xl bg-emerald-500 text-white shadow-xl shadow-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4 text-center sm:text-left">
          <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl shrink-0">
            ✓
          </div>
          <div>
            <h2 class="text-xl font-black">Registration Successfully Submitted!</h2>
            <p class="text-emerald-100 text-xs sm:text-sm mt-0.5">
              Your athlete profile is registered with the National Talent Identification Committee for KIYG 2026.
            </p>
          </div>
        </div>
        <button onclick="window.print()" class="shrink-0 px-5 py-2.5 bg-white text-emerald-800 font-bold text-xs uppercase tracking-wider rounded-xl shadow hover:bg-emerald-50 transition flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
          Print Slip
        </button>
      </div>

      <?php if ($athlete): ?>
        <!-- Official Printable Digital Athlete Pass -->
        <div class="athlete-pass mb-10">
          <div class="watermark">KISG</div>

          <!-- Pass Top Header -->
          <div class="athlete-pass-header">
            <div class="flex items-start justify-between">
              <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-orange-400 bg-white/10 px-2 py-0.5 rounded">
                  Ministry of Youth Affairs & Sports • Govt. of India
                </span>
                <h3 class="text-xl sm:text-2xl font-black text-white mt-1">
                  KHELO INDIA TALENT PASS
                </h3>
                <p class="text-xs text-slate-300">8th Khelo India Youth Games (KIYG 2026) • Telangana</p>
              </div>
              <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Application ID</span>
                <span class="text-lg font-black tracking-widest text-amber-400 font-mono"><?php echo htmlspecialchars($athlete['application_id']); ?></span>
              </div>
            </div>
          </div>

          <!-- Pass Body Content -->
          <div class="p-6 sm:p-8 bg-white space-y-6">
            
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 pb-6 border-b border-slate-100">
              <!-- Athlete Avatar / Photo Badge -->
              <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-orange-100 to-amber-200 border-2 border-orange-300 flex items-center justify-center text-4xl shadow-inner shrink-0">
                🏅
              </div>

              <div class="flex-1 text-center sm:text-left">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                  <h4 class="text-2xl font-black text-slate-900"><?php echo htmlspecialchars($athlete['full_name']); ?></h4>
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200">
                    <?php echo htmlspecialchars($athlete['age_category']); ?>
                  </span>
                </div>
                <p class="text-sm text-slate-600 font-medium">
                  <?php echo htmlspecialchars($athlete['school_name']); ?> (<?php echo htmlspecialchars($athlete['board']); ?>)
                </p>
                <p class="text-xs text-slate-400 mt-1">
                  DOB: <?php echo date('d M Y', strtotime($athlete['dob'])); ?> • Gender: <?php echo htmlspecialchars($athlete['gender']); ?> • Category: <?php echo htmlspecialchars($athlete['category']); ?>
                </p>
              </div>
            </div>

            <!-- Sporting Dossier Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Sport Discipline</span>
                <span class="font-extrabold text-slate-900 text-sm"><?php echo htmlspecialchars($athlete['sport_discipline']); ?></span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Sub-Event / Category</span>
                <span class="font-bold text-slate-900"><?php echo htmlspecialchars($athlete['sub_event']); ?></span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Home State / District</span>
                <span class="font-bold text-slate-900"><?php echo htmlspecialchars($athlete['district']); ?>, <?php echo htmlspecialchars($athlete['state']); ?></span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Qualifying Tier</span>
                <span class="font-bold text-blue-700"><?php echo htmlspecialchars($athlete['qualification_level']); ?></span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Current Status</span>
                <span class="font-black text-amber-600 uppercase"><?php echo htmlspecialchars($athlete['status']); ?></span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Registration Date</span>
                <span class="font-bold text-slate-900"><?php echo date('d M Y, h:i A', strtotime($athlete['created_at'])); ?></span>
              </div>
            </div>

            <!-- Scouting Venue & Next Stage Details -->
            <div class="p-4 rounded-xl bg-orange-50 border border-orange-200 text-xs">
              <span class="font-bold text-orange-900 uppercase text-[10px] block">Assigned Talent Scouting Assessment Venue:</span>
              <p class="text-orange-950 font-bold text-sm mt-0.5"><?php echo htmlspecialchars($athlete['trial_venue']); ?></p>
              <p class="text-orange-800 text-[11px] mt-1">
                Tentative Assessment Date: <strong><?php echo date('d F, Y', strtotime($athlete['trial_date'])); ?></strong>. 
                Keep original age proof, bonafide school letter, and sports kit ready.
              </p>
            </div>

            <!-- Barcode Graphic Simulation -->
            <div class="pt-4 border-t border-slate-100 flex flex-col items-center justify-center">
              <div class="barcode-box mb-2">
                <?php for ($b = 0; $b < 36; $b++): ?>
                  <span class="barcode-bar" style="width: <?php echo ($b % 3 == 0) ? '3px' : (($b % 2 == 0) ? '2px' : '1px'); ?>;"></span>
                <?php endfor; ?>
              </div>
              <span class="text-[10px] font-mono text-slate-400 tracking-widest"><?php echo htmlspecialchars($athlete['application_id']); ?></span>
            </div>

          </div>
        </div>
      <?php else: ?>
        <div class="p-8 bg-white rounded-3xl border border-slate-200 text-center">
          <p class="text-slate-600 font-semibold mb-4">Application pass not found or ID missing.</p>
          <a href="register.php" class="px-6 py-3 bg-orange-500 text-white font-bold rounded-xl text-sm">Register New Athlete →</a>
        </div>
      <?php endif; ?>

      <!-- 3-Step Next Roadmap -->
      <div class="no-print bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <h4 class="text-base font-black text-slate-900 mb-6 uppercase tracking-wider text-center">
          What Happens Next in the Selection Pipeline?
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="w-6 h-6 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center mb-2">1</span>
            <h5 class="font-bold text-slate-900 text-sm mb-1">Dossier Verification</h5>
            <p class="text-slate-500">
              The Talent Identification Committee validates your school enrollment and competition records against the NSRS repository.
            </p>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="w-6 h-6 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center mb-2">2</span>
            <h5 class="font-bold text-slate-900 text-sm mb-1">District / SAI Trials</h5>
            <p class="text-slate-500">
              Physical fitness tests and sports-specific technical trials are held at accredited centres across your region.
            </p>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center mb-2">3</span>
            <h5 class="font-bold text-slate-900 text-sm mb-1">KISCE Induction & Govt Grant</h5>
            <p class="text-slate-500">
              Top 1,000 scouted athletes are recommended to the Ministry of Youth Affairs & Sports for the Central Government's ₹5 Lakh/yr scholarship grant for 8 years (₹40 Lakh total).
            </p>
          </div>
        </div>

        <div class="mt-8 text-center">
          <a href="status.php?app_id=<?php echo urlencode($app_id); ?>" class="inline-flex items-center gap-2 text-blue-600 font-bold hover:underline text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            Track Application Live Status Anytime
          </a>
        </div>
      </div>

    </div>
  </main>

</body>
</html>

