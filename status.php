<?php
require_once __DIR__ . '/config.php';

$search_query = clean_input($_GET['query'] ?? $_GET['app_id'] ?? '');
$dob_query    = clean_input($_GET['dob'] ?? '');
$athlete      = null;
$searched     = false;
$error_msg    = null;

if (!empty($search_query)) {
    $searched = true;
    try {
        if (strpos($search_query, 'KISG26-') === 0 || strlen($search_query) === 13) {
            // Search by Application ID
            $stmt = $pdo->prepare("SELECT * FROM athletes WHERE application_id = :query LIMIT 1");
            $stmt->execute([':query' => $search_query]);
            $athlete = $stmt->fetch();
        } else {
            // Search by Phone and optional DOB
            $clean_phone = preg_replace('/\D/', '', $search_query);
            if (!empty($dob_query)) {
                $stmt = $pdo->prepare("SELECT * FROM athletes WHERE phone = :phone AND dob = :dob LIMIT 1");
                $stmt->execute([':phone' => $clean_phone, ':dob' => $dob_query]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM athletes WHERE phone = :phone LIMIT 1");
                $stmt->execute([':phone' => $clean_phone]);
            }
            $athlete = $stmt->fetch();
        }

        if (!$athlete) {
            $error_msg = "No registration record found matching the provided details. Please check the Application ID or registered mobile number.";
        }
    } catch (Exception $e) {
        $error_msg = "Database lookup error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Athlete Application Status | Super Sapiens</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-orange-500 selection:text-white">

  <div class="gov-strip"></div>

  <!-- Header -->
  <header class="bg-white border-b border-slate-200 py-3">
    <div class="container mx-auto px-4 sm:px-6 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-black flex items-center justify-center">SS</div>
        <span class="text-sm font-black text-slate-900 uppercase">Super Sapiens Portal</span>
      </a>
      <div class="flex items-center gap-2">
        <a href="register.php" class="text-xs font-bold text-orange-600 hover:text-orange-700 px-3 py-2">New Registration</a>
        <a href="index.php" class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-2">Home</a>
      </div>
    </div>
  </header>

  <main class="py-12 sm:py-20">
    <div class="container mx-auto px-4 sm:px-6 max-w-3xl">

      <div class="text-center mb-10">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-100 text-blue-700 mb-2">
          Real-time Verification Status
        </span>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">
          Track Athlete Application Status
        </h2>
        <p class="text-slate-600 text-sm mt-2">
          Enter your Application ID (e.g. <code>KISG26-784920</code>) or registered 10-digit mobile number.
        </p>
      </div>

      <!-- Search Card -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl mb-10">
        <form method="GET" action="status.php" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
              <label for="query" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Application ID or Mobile Number
              </label>
              <input type="text" id="query" name="query" required value="<?php echo htmlspecialchars($search_query); ?>" placeholder="e.g. KISG26-784920 or 9876543210"
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
            </div>

            <div>
              <label for="dob" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                DOB (Optional for Phone)
              </label>
              <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob_query); ?>"
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
            </div>
          </div>

          <div class="flex justify-end">
            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/25 transition">
              Search Status →
            </button>
          </div>
        </form>
      </div>

      <?php if ($error_msg): ?>
        <div class="p-6 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm mb-8 text-center font-medium">
          <?php echo htmlspecialchars($error_msg); ?>
        </div>
      <?php endif; ?>

      <?php if ($athlete): ?>
        <!-- Athlete Record Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden mb-8">
          
          <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
              <span class="text-[10px] font-extrabold uppercase tracking-widest text-orange-400 bg-white/10 px-2 py-0.5 rounded">
                Application Verified
              </span>
              <h3 class="text-2xl font-black mt-1"><?php echo htmlspecialchars($athlete['full_name']); ?></h3>
              <p class="text-xs text-slate-400">ID: <span class="font-mono text-amber-400"><?php echo htmlspecialchars($athlete['application_id']); ?></span></p>
            </div>

            <div>
              <?php
                $status_class = 'bg-amber-100 text-amber-800 border-amber-300';
                if ($athlete['status'] === 'Shortlisted for Trials') $status_class = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                if ($athlete['status'] === 'Verified') $status_class = 'bg-blue-100 text-blue-800 border-blue-300';
                if ($athlete['status'] === 'Selected for Scholarship') $status_class = 'bg-purple-100 text-purple-800 border-purple-300';
                if ($athlete['status'] === 'Rejected') $status_class = 'bg-red-100 text-red-800 border-red-300';
              ?>
              <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider border <?php echo $status_class; ?>">
                ● <?php echo htmlspecialchars($athlete['status']); ?>
              </span>
            </div>
          </div>

          <div class="p-6 sm:p-8 space-y-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Sport & Event</span>
                <span class="font-bold text-slate-900 text-sm"><?php echo htmlspecialchars($athlete['sport_discipline']); ?></span>
                <span class="text-slate-500 block text-[11px]"><?php echo htmlspecialchars($athlete['sub_event']); ?></span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Division</span>
                <span class="font-bold text-slate-900 text-sm"><?php echo htmlspecialchars($athlete['age_category']); ?></span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">State & District</span>
                <span class="font-bold text-slate-900"><?php echo htmlspecialchars($athlete['district']); ?>, <?php echo htmlspecialchars($athlete['state']); ?></span>
              </div>

              <div class="sm:col-span-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">School / Institution</span>
                <span class="font-bold text-slate-900"><?php echo htmlspecialchars($athlete['school_name']); ?> (<?php echo htmlspecialchars($athlete['board']); ?>)</span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 uppercase font-bold block text-[10px]">Applied On</span>
                <span class="font-bold text-slate-900"><?php echo date('d M Y', strtotime($athlete['created_at'])); ?></span>
              </div>
            </div>

            <!-- Trial Venue / Status Message -->
            <div class="p-5 rounded-2xl bg-blue-50 border border-blue-200 text-xs text-blue-950">
              <h4 class="font-bold text-sm text-blue-900 mb-1">Talent Assessment Center & Next Steps:</h4>
              <p>
                <strong>Venue:</strong> <?php echo htmlspecialchars($athlete['trial_venue'] ?? 'To be notified via SMS to ' . $athlete['phone']); ?>
              </p>
              <?php if (!empty($athlete['trial_date'])): ?>
                <p class="mt-1">
                  <strong>Reporting Date:</strong> <?php echo date('d F Y', strtotime($athlete['trial_date'])); ?> at 08:00 AM IST.
                </p>
              <?php endif; ?>
              <p class="text-blue-800 text-[11px] mt-2">
                Bring 2 copies of your printed pass, original Aadhaar card, bonafide school certificate, and standard competition footwear/attire.
              </p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
              <a href="success.php?app_id=<?php echo urlencode($athlete['application_id']); ?>" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow transition">
                View & Print Digital Pass →
              </a>
              <span class="text-xs text-slate-400">NSRS Sync Verified</span>
            </div>
          </div>

        </div>
      <?php endif; ?>

    </div>
  </main>

</body>
</html>

