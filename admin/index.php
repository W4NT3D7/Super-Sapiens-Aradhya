<?php
require_once __DIR__ . '/../config.php';

// Auth Protection
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Filters & Search
$filter_sport  = clean_input($_GET['sport'] ?? '');
$filter_state  = clean_input($_GET['state'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');
$filter_cat    = clean_input($_GET['age_category'] ?? '');
$search_text   = clean_input($_GET['search'] ?? '');

// Build High-Speed Filter Query
$where_clauses = [];
$params = [];

if (!empty($filter_sport)) {
    $where_clauses[] = "sport_discipline = :sport";
    $params[':sport'] = $filter_sport;
}

if (!empty($filter_state)) {
    $where_clauses[] = "state = :state";
    $params[':state'] = $filter_state;
}

if (!empty($filter_status)) {
    $where_clauses[] = "status = :status";
    $params[':status'] = $filter_status;
}

if (!empty($filter_cat)) {
    $where_clauses[] = "age_category = :cat";
    $params[':cat'] = $filter_cat;
}

if (!empty($search_text)) {
    $where_clauses[] = "(full_name LIKE :s1 OR application_id LIKE :s2 OR phone LIKE :s3 OR school_name LIKE :s4)";
    $params[':s1'] = "%$search_text%";
    $params[':s2'] = "%$search_text%";
    $params[':s3'] = "%$search_text%";
    $params[':s4'] = "%$search_text%";
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Summary Metrics
$stats = [
    'total' => 0,
    'u17'   => 0,
    'u21'   => 0,
    'shortlisted' => 0,
    'verified' => 0
];

try {
    $stats['total'] = $pdo->query("SELECT COUNT(*) FROM athletes")->fetchColumn();
    $stats['u17'] = $pdo->query("SELECT COUNT(*) FROM athletes WHERE age_category = 'Under-17'")->fetchColumn();
    $stats['u21'] = $pdo->query("SELECT COUNT(*) FROM athletes WHERE age_category = 'Under-21'")->fetchColumn();
    $stats['shortlisted'] = $pdo->query("SELECT COUNT(*) FROM athletes WHERE status = 'Shortlisted for Trials'")->fetchColumn();
    $stats['verified'] = $pdo->query("SELECT COUNT(*) FROM athletes WHERE status = 'Verified'")->fetchColumn();

    // Query Athletes List
    $sql = "SELECT * FROM athletes {$where_sql} ORDER BY id DESC LIMIT 200";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $athletes = $stmt->fetchAll();
} catch (Exception $e) {
    $athletes = [];
    $query_error = $e->getMessage();
}

$all_sports = get_sports_disciplines();
$all_states = get_indian_states();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Talent Scouting Management Console | Khelo India 2026</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased">

  <div class="gov-strip"></div>

  <!-- Admin Navigation Header -->
  <header class="bg-slate-900 text-white py-3.5 px-4 sm:px-8 border-b border-slate-800 sticky top-0 z-50">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-orange-500 text-white font-black flex items-center justify-center">KI</div>
        <div>
          <span class="text-[10px] uppercase font-bold text-orange-400 bg-white/10 px-2 py-0.5 rounded">Scouting Authority Portal</span>
          <h1 class="text-base font-black tracking-tight">KHELO INDIA TALENT MANAGEMENT</h1>
        </div>
      </div>

      <div class="flex items-center gap-4 text-xs">
        <span class="hidden md:inline text-slate-400">
          Logged in as: <strong class="text-white"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong> (<?php echo htmlspecialchars($_SESSION['admin_role']); ?>)
        </span>
        <a href="../index.php" target="_blank" class="text-slate-300 hover:text-white font-semibold">View Live Site ↗</a>
        <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-300 border border-red-500/30 hover:bg-red-500 hover:text-white transition font-bold">
          Sign Out
        </a>
      </div>
    </div>
  </header>

  <!-- Database Mode Banner -->
  <div class="bg-slate-800 text-slate-300 px-4 sm:px-8 py-2 text-xs border-b border-slate-700 flex flex-wrap items-center justify-between gap-2">
    <div class="flex items-center gap-2">
      <span class="w-2 h-2 rounded-full <?php echo ($db_engine === 'mysql') ? 'bg-emerald-400' : 'bg-amber-400'; ?>"></span>
      <span>
        Database Engine: <strong class="uppercase text-white font-mono"><?php echo htmlspecialchars($db_engine); ?></strong> 
        (<?php echo ($db_engine === 'mysql') ? 'Connected to MySQL on port ' . htmlspecialchars($db_port) : 'Active SQLite Standalone Mode'; ?>)
      </span>
    </div>
    <div class="text-[11px] text-slate-400">
      Optimized for 5,000+ Concurrent Requests • Indexed Query Engine
    </div>
  </div>

  <main class="py-8 px-4 sm:px-8 max-w-7xl mx-auto space-y-8">
    
    <!-- Top KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block">Total Applicants</span>
        <span class="text-3xl font-black text-slate-900 mt-1 block"><?php echo number_format($stats['total']); ?></span>
        <span class="text-[11px] text-slate-500 mt-1 block">National Talent Pool</span>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold uppercase text-orange-600 block">Under-17 School</span>
        <span class="text-3xl font-black text-orange-600 mt-1 block"><?php echo number_format($stats['u17']); ?></span>
        <span class="text-[11px] text-slate-500 mt-1 block">KISG Primary Division</span>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold uppercase text-blue-600 block">Under-21 Youth</span>
        <span class="text-3xl font-black text-blue-600 mt-1 block"><?php echo number_format($stats['u21']); ?></span>
        <span class="text-[11px] text-slate-500 mt-1 block">KIYG College Division</span>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-bold uppercase text-emerald-600 block">Shortlisted for Trials</span>
        <span class="text-3xl font-black text-emerald-600 mt-1 block"><?php echo number_format($stats['shortlisted']); ?></span>
        <span class="text-[11px] text-slate-500 mt-1 block">Active Trial Batches</span>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm col-span-2 md:col-span-1">
        <span class="text-xs font-bold uppercase text-purple-600 block">Dossiers Verified</span>
        <span class="text-3xl font-black text-purple-600 mt-1 block"><?php echo number_format($stats['verified']); ?></span>
        <span class="text-[11px] text-slate-500 mt-1 block">NSRS Record Matched</span>
      </div>
    </div>

    <!-- Filter & Action Controls -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
      <form method="GET" action="index.php" class="space-y-4">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
          <!-- Search input -->
          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Search Athlete / ID</label>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_text); ?>" placeholder="Name, ID, Phone..."
              class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-orange-500 font-semibold">
          </div>

          <!-- Sport filter -->
          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Sport Discipline</label>
            <select name="sport" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-orange-500 font-semibold">
              <option value="">All Disciplines (29)</option>
              <?php foreach ($all_sports as $group => $items): ?>
                <optgroup label="<?php echo htmlspecialchars($group); ?>">
                  <?php foreach ($items as $s): ?>
                    <option value="<?php echo htmlspecialchars($s); ?>" <?php echo ($filter_sport === $s) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($s); ?>
                    </option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- State filter -->
          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">State / UT</label>
            <select name="state" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-orange-500 font-semibold">
              <option value="">All States</option>
              <?php foreach ($all_states as $st): ?>
                <option value="<?php echo htmlspecialchars($st); ?>" <?php echo ($filter_state === $st) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($st); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Category filter -->
          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Age Division</label>
            <select name="age_category" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-orange-500 font-semibold">
              <option value="">All Divisions</option>
              <option value="Under-14" <?php echo ($filter_cat === 'Under-14') ? 'selected' : ''; ?>>Under-14</option>
              <option value="Under-17" <?php echo ($filter_cat === 'Under-17') ? 'selected' : ''; ?>>Under-17</option>
              <option value="Under-21" <?php echo ($filter_cat === 'Under-21') ? 'selected' : ''; ?>>Under-21</option>
            </select>
          </div>

          <!-- Status filter -->
          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-orange-500 font-semibold">
              <option value="">All Statuses</option>
              <option value="Under Review" <?php echo ($filter_status === 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
              <option value="Verified" <?php echo ($filter_status === 'Verified') ? 'selected' : ''; ?>>Verified</option>
              <option value="Shortlisted for Trials" <?php echo ($filter_status === 'Shortlisted for Trials') ? 'selected' : ''; ?>>Shortlisted for Trials</option>
              <option value="Selected for Scholarship" <?php echo ($filter_status === 'Selected for Scholarship') ? 'selected' : ''; ?>>Selected for Scholarship</option>
              <option value="Rejected" <?php echo ($filter_status === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
            </select>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <div class="flex items-center gap-2">
            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
              Apply Filters
            </button>
            <a href="index.php" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs rounded-xl transition">
              Reset
            </a>
          </div>

          <!-- 1-Click Export for Federation Officials -->
          <a href="export.php?<?php echo http_build_query($_GET); ?>" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Export to CSV / Excel</span>
          </a>
        </div>

      </form>
    </div>

    <!-- Registrations Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="p-5 border-b border-slate-200 flex items-center justify-between">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
          Athlete Applications List (Showing <?php echo count($athletes); ?> records)
        </h3>
        <span class="text-xs text-slate-400">Click Athlete ID to view full dossier</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-200">
            <tr>
              <th class="py-3 px-4">Application ID</th>
              <th class="py-3 px-4">Athlete & School</th>
              <th class="py-3 px-4">Sport & Event</th>
              <th class="py-3 px-4">Division</th>
              <th class="py-3 px-4">State / District</th>
              <th class="py-3 px-4">Qualifying Tier</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($athletes)): ?>
              <tr>
                <td colspan="8" class="py-8 text-center text-slate-400 font-semibold">
                  No registered athletes found matching the selected criteria.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($athletes as $a): ?>
                <tr class="hover:bg-slate-50 transition">
                  <td class="py-3 px-4">
                    <a href="view.php?id=<?php echo $a['id']; ?>" class="font-mono font-black text-orange-600 hover:underline">
                      <?php echo htmlspecialchars($a['application_id']); ?>
                    </a>
                    <span class="text-[10px] text-slate-400 block"><?php echo date('d M Y', strtotime($a['created_at'])); ?></span>
                  </td>

                  <td class="py-3 px-4">
                    <div class="font-bold text-slate-900"><?php echo htmlspecialchars($a['full_name']); ?></div>
                    <div class="text-[11px] text-slate-500 truncate max-w-xs"><?php echo htmlspecialchars($a['school_name']); ?></div>
                  </td>

                  <td class="py-3 px-4">
                    <div class="font-bold text-slate-900"><?php echo htmlspecialchars($a['sport_discipline']); ?></div>
                    <div class="text-[11px] text-slate-500 truncate max-w-xs"><?php echo htmlspecialchars($a['sub_event']); ?></div>
                  </td>

                  <td class="py-3 px-4">
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-800">
                      <?php echo htmlspecialchars($a['age_category']); ?>
                    </span>
                  </td>

                  <td class="py-3 px-4">
                    <span class="font-medium text-slate-800"><?php echo htmlspecialchars($a['district']); ?></span>,
                    <span class="text-slate-500"><?php echo htmlspecialchars($a['state']); ?></span>
                  </td>

                  <td class="py-3 px-4">
                    <span class="text-blue-700 font-semibold"><?php echo htmlspecialchars($a['qualification_level']); ?></span>
                  </td>

                  <td class="py-3 px-4">
                    <?php
                      $badge = 'bg-amber-100 text-amber-800';
                      if ($a['status'] === 'Shortlisted for Trials') $badge = 'bg-emerald-100 text-emerald-800';
                      if ($a['status'] === 'Verified') $badge = 'bg-blue-100 text-blue-800';
                      if ($a['status'] === 'Selected for Scholarship') $badge = 'bg-purple-100 text-purple-800';
                      if ($a['status'] === 'Rejected') $badge = 'bg-red-100 text-red-800';
                    ?>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?php echo $badge; ?>">
                      <?php echo htmlspecialchars($a['status']); ?>
                    </span>
                  </td>

                  <td class="py-3 px-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <a href="view.php?id=<?php echo $a['id']; ?>" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] transition">
                        View Dossier
                      </a>
                      <a href="../success.php?app_id=<?php echo urlencode($a['application_id']); ?>" target="_blank" class="p-1 text-slate-400 hover:text-slate-700" title="Print Pass">
                        🖨️
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

</body>
</html>

