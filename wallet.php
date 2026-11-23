<?php
require_once __DIR__ . '/config.php';
require_user_login();

$user = get_logged_in_user($pdo);

// Fetch user's transactions
$stmt_txns = $pdo->prepare("SELECT * FROM wallet_transactions WHERE user_id = :uid ORDER BY id DESC LIMIT 50");
$stmt_txns->execute([':uid' => $user['id']]);
$transactions = $stmt_txns->fetchAll();

// Calculate Margin currently locked in open trades
$stmt_margin = $pdo->prepare("SELECT COALESCE(SUM(margin_used), 0) FROM trades WHERE user_id = :uid AND status = 'OPEN'");
$stmt_margin->execute([':uid' => $user['id']]);
$locked_margin = (float)$stmt_margin->fetchColumn();

$csrf_token = get_csrf_token();
$page_title = 'In-App Wallet & Payment Interface | Super Sapiens';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-10 sm:py-14 flex-1">
  <div class="container mx-auto px-4 sm:px-6 max-w-5xl space-y-8">

    <!-- Top Headline Section (Requirement 7) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-1">
          Requirement 7: Payment Interface & App Wallet
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
          Super Sapiens App Wallet
        </h2>
        <p class="text-xs text-slate-400">Manage trading capital, top up funds via UPI, and track transaction history.</p>
      </div>

      <a href="trading.php" class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
        Go to Trading Desk →
      </a>
    </div>

    <!-- Wallet Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Primary Cash Balance -->
      <div class="bg-gradient-to-br from-slate-900 to-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Available Cash Balance</span>
        <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono mt-1" id="display-wallet-bal">
          ₹<?php echo number_format($user['wallet_balance'], 2); ?>
        </div>
        <p class="text-[11px] text-slate-400 mt-2">
          Unencumbered capital available for placing new F&O orders.
        </p>
      </div>

      <!-- Margin Locked in Active Trades -->
      <div class="bg-slate-950/80 p-6 rounded-3xl border border-slate-800 shadow-xl">
        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Margin Locked in Trades</span>
        <div class="text-3xl font-black text-orange-400 font-mono mt-1">
          ₹<?php echo number_format($locked_margin, 2); ?>
        </div>
        <p class="text-[11px] text-slate-400 mt-2">
          Collateral held in active open contracts. Released on square-off.
        </p>
      </div>

      <!-- Total Combined Equity -->
      <div class="bg-slate-950/80 p-6 rounded-3xl border border-slate-800 shadow-xl">
        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Total Portfolio Equity</span>
        <div class="text-3xl font-black text-white font-mono mt-1">
          ₹<?php echo number_format($user['wallet_balance'] + $locked_margin, 2); ?>
        </div>
        <p class="text-[11px] text-slate-400 mt-2">
          Net asset valuation across liquid wallet and active positions.
        </p>
      </div>

    </div>

    <!-- Payment Interface / Add Funds Box -->
    <div class="bg-slate-950/90 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6">
      <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
        <div>
          <h3 class="text-base font-black text-white uppercase tracking-wider">Instant Payment & Top-Up Interface</h3>
          <p class="text-xs text-slate-400">Add funds to your wallet instantly using UPI, Net Banking, or Debit Card</p>
        </div>
        <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-xl border border-emerald-500/20">
          ⚡ 0% Processing Fee
        </span>
      </div>

      <form id="add-funds-form" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <!-- Preset Amount Pills -->
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Select Amount to Add</label>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button type="button" class="preset-btn py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-sm font-black font-mono text-white transition active:scale-95" data-amt="1000">
              + ₹1,000
            </button>
            <button type="button" class="preset-btn py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-sm font-black font-mono text-white transition active:scale-95" data-amt="5000">
              + ₹5,000
            </button>
            <button type="button" class="preset-btn py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-sm font-black font-mono text-white transition active:scale-95" data-amt="10000">
              + ₹10,000
            </button>
            <button type="button" class="preset-btn py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-sm font-black font-mono text-white transition active:scale-95" data-amt="25000">
              + ₹25,000
            </button>
          </div>
        </div>

        <!-- Custom Amount Input -->
        <div>
          <label for="custom-amt" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
            Or Enter Custom Amount (₹)
          </label>
          <div class="relative">
            <span class="absolute left-4 top-3 text-base font-bold text-slate-500 font-mono">₹</span>
            <input type="number" id="custom-amt" name="amount" min="100" max="500000" step="100" value="5000" required
              class="w-full pl-9 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white font-mono font-bold text-lg focus:border-emerald-500 focus:outline-none">
          </div>
        </div>

        <!-- Payment Method Selection -->
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Select Payment Method</label>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/60 cursor-pointer">
              <input type="radio" name="payment_method" value="Instant UPI" checked class="text-emerald-500">
              <div>
                <span class="text-xs font-bold text-white block">⚡ Instant UPI</span>
                <span class="text-[10px] text-slate-400">Google Pay, PhonePe, Paytm, BHIM</span>
              </div>
            </label>

            <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/60 cursor-pointer">
              <input type="radio" name="payment_method" value="Debit / Credit Card" class="text-emerald-500">
              <div>
                <span class="text-xs font-bold text-white block">💳 Cards</span>
                <span class="text-[10px] text-slate-400">Visa, Mastercard, RuPay</span>
              </div>
            </label>

            <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/60 cursor-pointer">
              <input type="radio" name="payment_method" value="Net Banking" class="text-emerald-500">
              <div>
                <span class="text-xs font-bold text-white block">🏦 Net Banking</span>
                <span class="text-[10px] text-slate-400">SBI, HDFC, ICICI, Axis</span>
              </div>
            </label>
          </div>
        </div>

        <button type="submit" id="btn-add-funds" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-black text-sm rounded-2xl shadow-xl shadow-emerald-500/25 transition">
          Confirm Deposit & Add to Wallet →
        </button>
      </form>
    </div>

    <!-- Transaction History & Ledger -->
    <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-800">
        <div>
          <h3 class="text-base font-black text-white uppercase tracking-wider">Wallet Audit Ledger</h3>
          <p class="text-xs text-slate-400">All deposits, trade margin locks, and settlements</p>
        </div>
        <span class="text-[10px] text-slate-500">Showing last <?php echo count($transactions); ?> records</span>
      </div>

      <?php if (empty($transactions)): ?>
        <div class="py-8 text-center text-xs text-slate-500">
          No transactions recorded yet.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs font-mono">
            <thead class="text-[9px] uppercase font-bold text-slate-500 border-b border-slate-800">
              <tr>
                <th class="py-2.5 px-3">Date & Time</th>
                <th class="py-2.5 px-3">Description</th>
                <th class="py-2.5 px-3">Type</th>
                <th class="py-2.5 px-3">Amount</th>
                <th class="py-2.5 px-3">Balance After</th>
                <th class="py-2.5 px-3 text-right">Reference</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-semibold text-slate-300">
              <?php foreach ($transactions as $txn): 
                $amt = (float)$txn['amount'];
                $is_pos = ($amt >= 0);
              ?>
                <tr>
                  <td class="py-3 px-3 text-[11px] text-slate-400">
                    <?php echo date('d M Y, h:i A', strtotime($txn['created_at'])); ?>
                  </td>
                  <td class="py-3 px-3 font-bold text-white">
                    <?php echo htmlspecialchars($txn['description']); ?>
                  </td>
                  <td class="py-3 px-3">
                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-slate-900 border border-slate-800 text-slate-400">
                      <?php echo htmlspecialchars($txn['type']); ?>
                    </span>
                  </td>
                  <td class="py-3 px-3 font-bold <?php echo $is_pos ? 'text-emerald-400' : 'text-red-400'; ?>">
                    <?php echo ($is_pos ? '+' : '') . '₹' . number_format($amt, 2); ?>
                  </td>
                  <td class="py-3 px-3 text-slate-300">
                    ₹<?php echo number_format($txn['balance_after'], 2); ?>
                  </td>
                  <td class="py-3 px-3 text-right text-[10px] text-slate-500 font-mono">
                    <?php echo htmlspecialchars($txn['txn_ref']); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<script>
// Preset Button click handlers
document.querySelectorAll('.preset-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const amt = btn.getAttribute('data-amt');
    document.getElementById('custom-amt').value = amt;
  });
});

// Add Funds Form Submit Handler
const form = document.getElementById('add-funds-form');
const btn = document.getElementById('btn-add-funds');

if (form) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Processing Payment via Gateway...';
    }

    const formData = new FormData(form);

    try {
      const res = await fetch('wallet_add_funds.php', { method: 'POST', body: formData });
      const data = await res.json();

      if (data.status === 'success') {
        alert(data.message + ' (Ref: ' + data.txn_ref + ')');
        window.location.reload();
      } else {
        alert('Deposit notice: ' + data.message);
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Confirm Deposit & Add to Wallet →';
        }
      }
    } catch (err) {
      alert('Error during payment processing.');
      if (btn) {
        btn.disabled = false;
        btn.textContent = 'Confirm Deposit & Add to Wallet →';
      }
    }
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

