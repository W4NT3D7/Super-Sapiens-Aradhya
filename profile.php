<?php
require_once __DIR__ . '/config.php';
require_user_login();

$user = get_logged_in_user($pdo);
$msg = null;
$error = null;

// Handle profile settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $full_name = clean_input($_POST['full_name'] ?? '');
        $phone     = clean_input(preg_replace('/\D/', '', $_POST['phone'] ?? ''));
        $sports_arr= $_POST['favorite_sports'] ?? [];
        $fav_sports= implode(',', array_map('clean_input', (array)$sports_arr));

        if (!empty($full_name)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET full_name = :name, phone = :phone, favorite_sports = :sports WHERE id = :uid");
                $stmt->execute([
                    ':name'   => $full_name,
                    ':phone'  => $phone ?: NULL,
                    ':sports' => $fav_sports,
                    ':uid'    => $user['id']
                ]);
                $msg = 'Profile preferences updated successfully!';
                $user = get_logged_in_user($pdo); // Refresh user data
            } catch (Exception $e) {
                $error = 'Failed to update profile: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'update_password') {
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass     = $_POST['new_password'] ?? '';

        if (!password_verify($current_pass, $user['password_hash'])) {
            $error = 'Current password does not match.';
        } elseif (strlen($new_pass) < 6) {
            $error = 'New password must be at least 6 characters.';
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = :p WHERE id = :uid");
            $stmt->execute([':p' => $new_hash, ':uid' => $user['id']]);
            $msg = 'Password changed successfully.';
        }
    }
}

// Fetch user's open positions & recent activity
$open_trades = [];
$total_margin_used = 0;
$total_unrealized_pnl = 0;

try {
    $stmt_trades = $pdo->prepare("
        SELECT t.*, c.ticker, c.contract_title, a.full_name as athlete_name, a.sport_discipline, a.avatar_icon
        FROM trades t
        JOIN athlete_contracts c ON t.contract_id = c.id
        JOIN athletes a ON c.athlete_id = a.id
        WHERE t.user_id = :uid AND t.status = 'OPEN'
        ORDER BY t.id DESC
    ");
    $stmt_trades->execute([':uid' => $user['id']]);
    $open_trades = $stmt_trades->fetchAll();

    foreach ($open_trades as $t) {
        $total_margin_used += (float)$t['margin_used'];
        $total_unrealized_pnl += (float)$t['pnl'];
    }
} catch (Exception $e) {
    // Graceful handling
}

$user_sports = array_map('trim', explode(',', $user['favorite_sports'] ?? ''));
$page_title = 'User Profile & Settings | Super Sapiens';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-10 sm:py-14 flex-1">
  <div class="container mx-auto px-4 sm:px-6 max-w-5xl space-y-8">

    <!-- Top Profile Banner (Requirement 2: Dedicated Profile with Icon) -->
    <div class="reveal reveal-pop premium-card bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-700/80 shadow-2xl relative overflow-hidden">
      <div class="absolute -right-16 -top-16 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative z-10">
        
        <div class="flex items-center gap-5">
          <!-- Profile Avatar Icon -->
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-orange-500/30 border-2 border-orange-400/30 shrink-0">
            <?php echo strtoupper(substr($user['full_name'], 0, 2)); ?>
          </div>

          <div>
            <div class="flex items-center gap-2 mb-1">
              <h2 class="text-2xl font-black text-white"><?php echo htmlspecialchars($user['full_name']); ?></h2>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                ✓ <?php echo htmlspecialchars($user['kyc_status']); ?>
              </span>
            </div>
            <p class="text-xs text-slate-400">
              @<?php echo htmlspecialchars($user['username']); ?> • <?php echo htmlspecialchars($user['email']); ?>
            </p>
            <p class="text-[11px] text-slate-500 mt-1">
              Member Since: <?php echo date('d F, Y', strtotime($user['created_at'])); ?> • Trader ID: #TX-<?php echo str_pad($user['id'], 5, '0', STR_PAD_LEFT); ?>
            </p>
          </div>
        </div>

        <!-- Quick Wallet Card on Profile -->
        <div class="bg-slate-950/70 p-4 rounded-2xl border border-slate-800 text-right shrink-0">
          <span class="text-[10px] uppercase font-bold text-slate-400 block">Available Cash Balance</span>
          <span class="text-2xl font-black text-emerald-400 font-mono">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
          <div class="mt-2 flex items-center justify-end gap-2">
            <a href="wallet.php" class="px-3 py-1 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-xs rounded-lg transition shadow">
              + Add Funds
            </a>
            <a href="trading.php" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-lg transition">
              Trade F&O →
            </a>
          </div>
        </div>

      </div>
    </div>

    <!-- Feedback messages -->
    <?php if ($msg): ?>
      <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold text-center">
        <?php echo htmlspecialchars($msg); ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="p-4 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-300 text-xs font-bold text-center">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <!-- Portfolio Metrics Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-slate-950/80 p-5 rounded-2xl border border-slate-800 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Cash Balance</span>
        <span class="text-xl font-black text-white font-mono mt-1 block">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
      </div>

      <div class="bg-slate-950/80 p-5 rounded-2xl border border-slate-800 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Margin In Trades</span>
        <span class="text-xl font-black text-orange-400 font-mono mt-1 block">₹<?php echo number_format($total_margin_used, 2); ?></span>
      </div>

      <div class="bg-slate-950/80 p-5 rounded-2xl border border-slate-800 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Unrealized P&L</span>
        <span class="text-xl font-black font-mono mt-1 block <?php echo ($total_unrealized_pnl >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
          <?php echo ($total_unrealized_pnl >= 0 ? '+' : '') . '₹' . number_format($total_unrealized_pnl, 2); ?>
        </span>
      </div>

      <div class="bg-slate-950/80 p-5 rounded-2xl border border-slate-800 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Open Positions</span>
        <span class="text-xl font-black text-blue-400 font-mono mt-1 block"><?php echo count($open_trades); ?> Contracts</span>
      </div>
    </div>

    <!-- Open Positions Summary -->
    <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-sm">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-base font-black text-white uppercase tracking-wider">Active Athlete F&O Contracts</h3>
          <p class="text-xs text-slate-400">Contracts currently in your portfolio</p>
        </div>
        <a href="trading.php" class="text-xs font-bold text-orange-400 hover:text-orange-300">Open Full Trading Desk →</a>
      </div>

      <?php if (empty($open_trades)): ?>
        <div class="p-8 text-center text-slate-500 text-xs">
          You currently have no open positions. 
          <a href="discover.php" class="text-orange-400 underline font-bold ml-1">Explore Athlete Talents</a> to take your first position.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="text-[10px] uppercase font-bold text-slate-500 border-b border-slate-800">
              <tr>
                <th class="py-2.5 px-3">Athlete & Contract</th>
                <th class="py-2.5 px-3">Side</th>
                <th class="py-2.5 px-3">Qty</th>
                <th class="py-2.5 px-3">Entry Price</th>
                <th class="py-2.5 px-3">Current</th>
                <th class="py-2.5 px-3">P&L</th>
                <th class="py-2.5 px-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-medium">
              <?php foreach ($open_trades as $t): ?>
                <tr>
                  <td class="py-3 px-3">
                    <div class="font-bold text-white flex items-center gap-1.5">
                      <span><?php echo $t['avatar_icon']; ?></span>
                      <span><?php echo htmlspecialchars($t['athlete_name']); ?></span>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400"><?php echo htmlspecialchars($t['ticker']); ?></span>
                  </td>
                  <td class="py-3 px-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black <?php echo ($t['direction'] === 'BUY') ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'; ?>">
                      <?php echo $t['direction']; ?>
                    </span>
                  </td>
                  <td class="py-3 px-3 font-mono"><?php echo $t['quantity']; ?></td>
                  <td class="py-3 px-3 font-mono">₹<?php echo number_format($t['entry_price'], 2); ?></td>
                  <td class="py-3 px-3 font-mono">₹<?php echo number_format($t['current_price'], 2); ?></td>
                  <td class="py-3 px-3 font-mono font-bold <?php echo ($t['pnl'] >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
                    <?php echo ($t['pnl'] >= 0 ? '+' : '') . '₹' . number_format($t['pnl'], 2); ?>
                  </td>
                  <td class="py-3 px-3 text-right">
                    <a href="trading.php?ticker=<?php echo urlencode($t['ticker']); ?>" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white text-[11px] font-bold">
                      Trade
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Account Details & Preferences Form -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Profile Information & Sports Preferences -->
      <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-sm">
        <h3 class="text-sm font-black text-white uppercase tracking-wider mb-4">Account Information & News Algorithm</h3>
        
        <form method="POST" action="profile.php" class="space-y-4">
          <input type="hidden" name="action" value="update_profile">

          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Full Name</label>
            <input type="text" name="full_name" required value="<?php echo htmlspecialchars($user['full_name']); ?>"
              class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs font-semibold focus:border-orange-500 focus:outline-none">
          </div>

          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Mobile Phone</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="10-digit number"
              class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs font-semibold focus:border-orange-500 focus:outline-none">
          </div>

          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-2">
              Sports Interests (Personalized Feed Preferences):
            </label>
            <div class="grid grid-cols-2 gap-2 text-xs">
              <?php
              $all_sample_sports = ['Athletics', 'Kalaripayattu', 'Swimming', 'Mallakhamba', 'Gatka', 'Boxing', 'Wrestling', 'Archery'];
              foreach ($all_sample_sports as $sp):
                $is_checked = in_array($sp, $user_sports);
              ?>
                <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 text-xs cursor-pointer">
                  <input type="checkbox" name="favorite_sports[]" value="<?php echo $sp; ?>" <?php echo $is_checked ? 'checked' : ''; ?> class="rounded text-orange-500">
                  <span><?php echo $sp; ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow transition">
            Save Preferences
          </button>
        </form>
      </div>

      <!-- Security / Change Password -->
      <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-sm">
        <h3 class="text-sm font-black text-white uppercase tracking-wider mb-4">Security & Password</h3>

        <form method="POST" action="profile.php" class="space-y-4">
          <input type="hidden" name="action" value="update_password">

          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Current Password</label>
            <input type="password" name="current_password" required placeholder="••••••••••••"
              class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs font-semibold focus:border-orange-500 focus:outline-none">
          </div>

          <div>
            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">New Password</label>
            <input type="password" name="new_password" required placeholder="Minimum 6 characters"
              class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs font-semibold focus:border-orange-500 focus:outline-none">
          </div>

          <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition">
            Update Password
          </button>
        </form>

        <div class="mt-8 pt-4 border-t border-slate-800 text-slate-500 text-[11px]">
          Session Token: Protected with HTTP-Only Lax cookies and salted bcrypt encryption.
        </div>
      </div>

    </div>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

