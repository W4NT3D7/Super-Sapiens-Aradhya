<?php
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$app_id = clean_input($_GET['app_id'] ?? '');

$athlete = null;
try {
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM athletes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $athlete = $stmt->fetch();
    } elseif (!empty($app_id)) {
        $stmt = $pdo->prepare("SELECT * FROM athletes WHERE application_id = ? LIMIT 1");
        $stmt->execute([$app_id]);
        $athlete = $stmt->fetch();
    }
} catch (Exception $e) {
    $athlete = null;
}

if (!$athlete) {
    header('Location: index.php');
    exit;
}

$csrf_token = get_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Athlete Dossier: <?php echo htmlspecialchars($athlete['full_name']); ?> | Khelo India 2026</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased">

  <div class="gov-strip"></div>

  <!-- Header -->
  <header class="bg-slate-900 text-white py-3.5 px-6 border-b border-slate-800">
    <div class="max-w-5xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="index.php" class="text-xs text-slate-400 hover:text-white font-bold">← Back to Dashboard</a>
        <span class="text-slate-600">•</span>
        <span class="text-xs font-mono text-amber-400 font-bold"><?php echo htmlspecialchars($athlete['application_id']); ?></span>
      </div>
      <div class="flex items-center gap-3">
        <a href="../success.php?app_id=<?php echo urlencode($athlete['application_id']); ?>" target="_blank" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-lg transition">
          Print Pass 🖨️
        </a>
      </div>
    </div>
  </header>

  <main class="py-10 px-4 max-w-5xl mx-auto space-y-6">

    <!-- Dossier Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
      <div class="flex items-center gap-5">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-100 to-amber-200 border-2 border-orange-300 flex items-center justify-center text-3xl font-bold shadow-inner shrink-0">
          🇮🇳
        </div>
        <div>
          <div class="flex items-center gap-2 mb-1">
            <h2 class="text-2xl font-black text-slate-900"><?php echo htmlspecialchars($athlete['full_name']); ?></h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200">
              <?php echo htmlspecialchars($athlete['age_category']); ?>
            </span>
          </div>
          <p class="text-sm font-semibold text-slate-600">
            <?php echo htmlspecialchars($athlete['school_name']); ?> • <span class="text-orange-600 font-bold"><?php echo htmlspecialchars($athlete['board']); ?></span>
          </p>
          <p class="text-xs text-slate-400 mt-1">
            Registered: <?php echo date('d M Y, h:i A', strtotime($athlete['created_at'])); ?> • State: <?php echo htmlspecialchars($athlete['state']); ?> (<?php echo htmlspecialchars($athlete['district']); ?>)
          </p>
        </div>
      </div>

      <div class="text-right">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Current Status</span>
        <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-300">
          ● <?php echo htmlspecialchars($athlete['status']); ?>
        </span>
      </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Column 1: Identity & Contact -->
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 text-xs">
        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
          Personal Identity
        </h3>
        
        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Date of Birth & Age</span>
          <span class="font-bold text-slate-800 text-sm"><?php echo date('d M Y', strtotime($athlete['dob'])); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Gender & Social Category</span>
          <span class="font-bold text-slate-800"><?php echo htmlspecialchars($athlete['gender']); ?> • <?php echo htmlspecialchars($athlete['category']); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Guardian / Parent</span>
          <span class="font-bold text-slate-800"><?php echo htmlspecialchars($athlete['guardian_name']); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Phone Number</span>
          <span class="font-bold text-slate-800 font-mono"><?php echo htmlspecialchars($athlete['phone']); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Email Address</span>
          <span class="font-bold text-slate-800"><?php echo htmlspecialchars($athlete['email']); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Aadhaar Security Hash</span>
          <span class="font-mono text-[10px] text-slate-500 break-all"><?php echo htmlspecialchars($athlete['aadhaar_hash']); ?></span>
        </div>
      </div>

      <!-- Column 2: Sporting Credentials -->
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 text-xs">
        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
          Sporting Profile
        </h3>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Sport Discipline</span>
          <span class="font-black text-orange-600 text-base"><?php echo htmlspecialchars($athlete['sport_discipline']); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Sub-Event / Category</span>
          <span class="font-bold text-slate-800 text-sm"><?php echo htmlspecialchars($athlete['sub_event']); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Playing Experience</span>
          <span class="font-bold text-slate-800"><?php echo (int)$athlete['playing_experience']; ?> Years of Competitive Practice</span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Qualifying Competition</span>
          <span class="font-bold text-blue-700 text-sm"><?php echo htmlspecialchars($athlete['qualification_level']); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Achievements & Medals</span>
          <p class="text-slate-700 italic bg-slate-50 p-3 rounded-xl border border-slate-100">
            "<?php echo nl2br(htmlspecialchars($athlete['achievement_details'])); ?>"
          </p>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Certificate / License Ref</span>
          <span class="font-mono font-bold text-slate-800"><?php echo htmlspecialchars($athlete['certificate_no'] ?? 'N/A'); ?></span>
        </div>

        <div>
          <span class="text-slate-400 font-bold uppercase block text-[10px]">Coach / Academy</span>
          <span class="font-bold text-slate-800"><?php echo htmlspecialchars($athlete['coach_name'] ?? 'Self'); ?> (<?php echo htmlspecialchars($athlete['coach_phone'] ?? 'N/A'); ?>)</span>
        </div>
      </div>

      <!-- Column 3: Status & Trial Assignment -->
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 text-xs">
        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
          Update Status & Trials
        </h3>

        <form method="POST" action="update_status.php" class="space-y-4">
          <input type="hidden" name="id" value="<?php echo $athlete['id']; ?>">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

          <div>
            <label class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Application Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-orange-500 font-bold">
              <option value="Under Review" <?php echo ($athlete['status'] === 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
              <option value="Verified" <?php echo ($athlete['status'] === 'Verified') ? 'selected' : ''; ?>>Verified</option>
              <option value="Shortlisted for Trials" <?php echo ($athlete['status'] === 'Shortlisted for Trials') ? 'selected' : ''; ?>>Shortlisted for Trials</option>
              <option value="Selected for Scholarship" <?php echo ($athlete['status'] === 'Selected for Scholarship') ? 'selected' : ''; ?>>Selected for Scholarship (₹5L/yr)</option>
              <option value="Rejected" <?php echo ($athlete['status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
            </select>
          </div>

          <div>
            <label class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Trial Venue / KISCE Centre</label>
            <input type="text" name="trial_venue" value="<?php echo htmlspecialchars($athlete['trial_venue'] ?? ''); ?>" placeholder="e.g. GMC Balayogi Stadium, Hyderabad"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-orange-500 font-semibold">
          </div>

          <div>
            <label class="block text-slate-400 font-bold uppercase text-[10px] mb-1">Trial Date</label>
            <input type="date" name="trial_date" value="<?php echo htmlspecialchars($athlete['trial_date'] ?? ''); ?>"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-orange-500 font-semibold">
          </div>

          <button type="submit" class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow transition">
            Save Status & Notify Athlete →
          </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400 leading-relaxed">
          Updating status automatically updates the candidate's live record on the public status tracker and adjusts the digital pass.
        </div>
      </div>

    </div>

  </main>

</body>
</html>

