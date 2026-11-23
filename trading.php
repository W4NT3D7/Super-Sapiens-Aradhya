<?php
require_once __DIR__ . '/config.php';
require_user_login();

$user = get_logged_in_user($pdo);
$ticker = clean_input($_GET['ticker'] ?? 'AARAV-100M-DEC26');

// Fetch all active contracts for Watchlist
$contracts = $pdo->query("
    SELECT c.*, a.full_name as athlete_name, a.avatar_icon, a.sport_discipline,
           ((c.current_price - c.previous_close) / c.previous_close * 100) as pct_change
    FROM athlete_contracts c
    JOIN athletes a ON c.athlete_id = a.id
    ORDER BY c.volume_contracts DESC
")->fetchAll();

// Select active contract
$active_contract = null;
foreach ($contracts as $c) {
    if ($c['ticker'] === $ticker) {
        $active_contract = $c;
        break;
    }
}
if (!$active_contract && !empty($contracts)) {
    $active_contract = $contracts[0];
}

// Fetch user open positions
$stmt_open = $pdo->prepare("
    SELECT t.*, c.ticker, c.current_price as market_price, a.full_name as athlete_name, a.avatar_icon
    FROM trades t
    JOIN athlete_contracts c ON t.contract_id = c.id
    JOIN athletes a ON c.athlete_id = a.id
    WHERE t.user_id = :uid AND t.status = 'OPEN'
    ORDER BY t.id DESC
");
$stmt_open->execute([':uid' => $user['id']]);
$open_trades = $stmt_open->fetchAll();

$csrf_token = get_csrf_token();
$page_title = 'F&O Athlete Trading Desk • ' . htmlspecialchars($active_contract['ticker'] ?? 'Super Sapiens');
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-6 flex-1">
  <div class="container mx-auto px-4 sm:px-6 max-w-7xl space-y-6">

    <!-- Top Requirement Headline -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
      <div>
        <div class="flex items-center gap-2">
          <span class="text-[9px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">
            Requirement 6: F&O Trading Interface
          </span>
          <span class="text-xs text-slate-400">Futures & Options on Grassroots Sports Talent</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-1">
          Super Sapiens Exchange (F&O Trading Desk)
        </h2>
      </div>

      <!-- Quick Balance -->
      <div class="flex items-center gap-3">
        <div class="text-right">
          <span class="text-[10px] uppercase font-bold text-slate-400 block">Available Trading Capital</span>
          <span class="text-base font-black text-emerald-400 font-mono" id="desk-wallet-balance">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
        </div>
        <a href="wallet.php" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-xs rounded-xl shadow transition">
          + Top Up
        </a>
      </div>
    </div>

    <!-- Main Trading Grid: Left (Watchlist 1 col), Center (Chart & Positions 2 cols), Right (Order Ticket 1 col) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

      <!-- Column 1: Watchlist & Market Tickers -->
      <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-4 space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
          <span class="text-xs font-black uppercase text-slate-400 tracking-wider">Talent Contracts</span>
          <span class="text-[10px] text-slate-500">Live F&O</span>
        </div>

        <div class="space-y-2">
          <?php foreach ($contracts as $c): 
            $is_active = ($c['ticker'] === $active_contract['ticker']);
            $pct = (float)$c['pct_change'];
          ?>
            <a href="trading.php?ticker=<?php echo urlencode($c['ticker']); ?>"
               class="p-3 rounded-2xl block transition <?php echo $is_active ? 'bg-slate-800 border-l-4 border-orange-500 shadow-lg' : 'bg-slate-900/60 hover:bg-slate-900 border border-slate-800/80'; ?>">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="text-base"><?php echo $c['avatar_icon']; ?></span>
                  <div>
                    <span class="text-xs font-black text-white block"><?php echo htmlspecialchars($c['ticker']); ?></span>
                    <span class="text-[10px] text-slate-400 block truncate max-w-[120px]"><?php echo htmlspecialchars($c['athlete_name']); ?></span>
                  </div>
                </div>
                <div class="text-right">
                  <span class="text-xs font-mono font-black text-white block">₹<?php echo number_format($c['current_price'], 2); ?></span>
                  <span class="text-[10px] font-bold block <?php echo ($pct >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
                    <?php echo ($pct >= 0 ? '+' : '') . number_format($pct, 2); ?>%
                  </span>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>

        <div class="pt-3 border-t border-slate-800 text-[10px] text-slate-500 text-center">
          Contract Expiry: Dec 2026 (KIYG Telangana Finals)
        </div>
      </div>

      <!-- Column 2 & 3: Main Chart, Order Book, Positions (2 Cols Wide) -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Active Contract Banner & Live Metrics -->
        <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-5 space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
              <div class="flex items-center gap-2">
                <span class="text-xl"><?php echo $active_contract['avatar_icon']; ?></span>
                <h3 class="text-lg font-black text-white font-mono"><?php echo htmlspecialchars($active_contract['ticker']); ?></h3>
                <span class="text-[10px] font-bold bg-slate-800 text-slate-300 px-2 py-0.5 rounded">FUTURES</span>
              </div>
              <p class="text-xs text-slate-400 mt-0.5"><?php echo htmlspecialchars($active_contract['contract_title']); ?></p>
            </div>

            <div class="text-right">
              <span class="text-2xl font-black text-white font-mono">₹<?php echo number_format($active_contract['current_price'], 2); ?></span>
              <span class="text-xs font-bold block <?php echo ($active_contract['pct_change'] >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
                <?php echo ($active_contract['pct_change'] >= 0 ? '+' : '') . number_format($active_contract['pct_change'], 2); ?>% Today
              </span>
            </div>
          </div>

          <!-- Market Depth Summary Bar -->
          <div class="grid grid-cols-4 gap-2 text-center text-xs bg-slate-900 p-2.5 rounded-2xl border border-slate-800 font-mono">
            <div>
              <span class="text-[9px] text-slate-500 block uppercase">24h High</span>
              <span class="text-slate-200 font-bold">₹<?php echo number_format($active_contract['high_24h'], 2); ?></span>
            </div>
            <div>
              <span class="text-[9px] text-slate-500 block uppercase">24h Low</span>
              <span class="text-slate-200 font-bold">₹<?php echo number_format($active_contract['low_24h'], 2); ?></span>
            </div>
            <div>
              <span class="text-[9px] text-slate-500 block uppercase">Volume</span>
              <span class="text-slate-200 font-bold"><?php echo number_format($active_contract['volume_contracts']); ?></span>
            </div>
            <div>
              <span class="text-[9px] text-slate-500 block uppercase">Open Int</span>
              <span class="text-slate-200 font-bold"><?php echo number_format($active_contract['open_interest']); ?></span>
            </div>
          </div>

          <!-- Interactive High-Performance Simulated Candlestick / Price Chart -->
          <div class="space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-400 text-[11px] uppercase tracking-wider">Performance Trend</span>
              <div class="flex items-center gap-1">
                <button class="px-2 py-0.5 rounded bg-slate-800 text-[10px] font-bold text-white hover:bg-slate-700">1D</button>
                <button class="px-2 py-0.5 rounded bg-slate-900 text-[10px] font-bold text-slate-400 hover:text-white">1W</button>
                <button class="px-2 py-0.5 rounded bg-slate-900 text-[10px] font-bold text-slate-400 hover:text-white">1M</button>
                <button class="px-2 py-0.5 rounded bg-slate-900 text-[10px] font-bold text-slate-400 hover:text-white">ALL</button>
              </div>
            </div>

            <!-- SVG Candlestick / Trend Chart Visualization -->
            <div class="w-full h-48 bg-slate-900 rounded-2xl p-4 border border-slate-800 flex items-end justify-between relative overflow-hidden">
              <!-- Grid Lines -->
              <div class="absolute inset-0 flex flex-col justify-between p-4 pointer-events-none opacity-20">
                <div class="border-b border-slate-700 w-full"></div>
                <div class="border-b border-slate-700 w-full"></div>
                <div class="border-b border-slate-700 w-full"></div>
              </div>

              <!-- Animated Bars / Price Bars -->
              <?php
                // Generate stylized chart bars based on current price
                $base = (float)$active_contract['current_price'];
                $seed_vals = [0.92, 0.94, 0.93, 0.96, 0.95, 0.98, 0.97, 1.01, 0.99, 1.03, 1.02, 1.05];
                foreach ($seed_vals as $idx => $multiplier):
                  $h_pct = min(95, max(20, ($multiplier - 0.85) * 450));
                  $is_up = ($multiplier >= 0.97);
              ?>
                <div class="flex-1 flex flex-col items-center justify-end h-full px-1 relative z-10 group cursor-pointer">
                  <div class="text-[9px] font-mono opacity-0 group-hover:opacity-100 transition absolute -top-5 text-white bg-slate-800 px-1 rounded">
                    ₹<?php echo round($base * $multiplier, 1); ?>
                  </div>
                  <div class="w-full max-w-[14px] rounded-t transition-all duration-300 <?php echo $is_up ? 'bg-emerald-500 hover:bg-emerald-400' : 'bg-red-500 hover:bg-red-400'; ?>"
                       style="height: <?php echo $h_pct; ?>%;"></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

        </div>

        <!-- Order Book Depth (Simulated Live Bids & Asks) -->
        <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-5">
          <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h4 class="text-xs font-black uppercase text-white tracking-wider">Live Order Book Depth</h4>
            <span class="text-[10px] text-slate-500 font-mono">1 Lot = <?php echo $active_contract['lot_size']; ?> Contracts</span>
          </div>

          <div class="grid grid-cols-2 gap-4 text-xs font-mono mt-3">
            <!-- Bids (Green) -->
            <div>
              <div class="flex justify-between text-[10px] text-slate-500 uppercase pb-1 border-b border-slate-800">
                <span>Bid Qty</span>
                <span>Price (₹)</span>
              </div>
              <div class="space-y-1 mt-1 text-emerald-400 font-semibold">
                <div class="flex justify-between"><span>1,200</span><span>₹<?php echo number_format($active_contract['current_price'] - 0.5, 2); ?></span></div>
                <div class="flex justify-between"><span>850</span><span>₹<?php echo number_format($active_contract['current_price'] - 1.2, 2); ?></span></div>
                <div class="flex justify-between"><span>2,400</span><span>₹<?php echo number_format($active_contract['current_price'] - 2.0, 2); ?></span></div>
              </div>
            </div>

            <!-- Asks (Red) -->
            <div>
              <div class="flex justify-between text-[10px] text-slate-500 uppercase pb-1 border-b border-slate-800">
                <span>Price (₹)</span>
                <span>Ask Qty</span>
              </div>
              <div class="space-y-1 mt-1 text-red-400 font-semibold">
                <div class="flex justify-between"><span>₹<?php echo number_format($active_contract['current_price'] + 0.5, 2); ?></span><span>940</span></div>
                <div class="flex justify-between"><span>₹<?php echo number_format($active_contract['current_price'] + 1.0, 2); ?></span><span>1,620</span></div>
                <div class="flex justify-between"><span>₹<?php echo number_format($active_contract['current_price'] + 1.8, 2); ?></span><span>3,100</span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Open Positions Section with Instant Square-Off Action -->
        <div class="bg-slate-950/80 rounded-3xl border border-slate-800 p-5 space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <h4 class="text-xs font-black uppercase text-white tracking-wider">
              Your Open Positions (<?php echo count($open_trades); ?>)
            </h4>
            <span class="text-[10px] text-slate-500">Live Mark-to-Market P&L</span>
          </div>

          <?php if (empty($open_trades)): ?>
            <div class="py-6 text-center text-xs text-slate-500">
              No open positions. Place an order using the terminal on the right!
            </div>
          <?php else: ?>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs font-mono">
                <thead class="text-[9px] uppercase text-slate-500 border-b border-slate-800">
                  <tr>
                    <th class="py-2 px-2">Contract</th>
                    <th class="py-2 px-2">Side</th>
                    <th class="py-2 px-2">Qty</th>
                    <th class="py-2 px-2">Entry</th>
                    <th class="py-2 px-2">Current</th>
                    <th class="py-2 px-2">P&L</th>
                    <th class="py-2 px-2 text-right">Square Off</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-semibold text-slate-300">
                  <?php foreach ($open_trades as $t): 
                    $curr_p = (float)$t['market_price'];
                    $ent_p = (float)$t['entry_price'];
                    $qty = (int)$t['quantity'];
                    $pnl = ($t['direction'] === 'BUY') ? ($curr_p - $ent_p) * $qty : ($ent_p - $curr_p) * $qty;
                  ?>
                    <tr>
                      <td class="py-2.5 px-2">
                        <span class="text-white block font-bold"><?php echo htmlspecialchars($t['ticker']); ?></span>
                      </td>
                      <td class="py-2.5 px-2">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black <?php echo ($t['direction'] === 'BUY') ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'; ?>">
                          <?php echo $t['direction']; ?>
                        </span>
                      </td>
                      <td class="py-2.5 px-2"><?php echo $t['quantity']; ?></td>
                      <td class="py-2.5 px-2">₹<?php echo number_format($t['entry_price'], 2); ?></td>
                      <td class="py-2.5 px-2">₹<?php echo number_format($curr_p, 2); ?></td>
                      <td class="py-2.5 px-2 font-bold <?php echo ($pnl >= 0) ? 'text-emerald-400' : 'text-red-400'; ?>">
                        <?php echo ($pnl >= 0 ? '+' : '') . '₹' . number_format($pnl, 2); ?>
                      </td>
                      <td class="py-2.5 px-2 text-right">
                        <button onclick="squareOff(<?php echo $t['id']; ?>)" class="px-2.5 py-1 rounded-lg bg-red-500/20 text-red-300 hover:bg-red-500 hover:text-white text-[10px] font-black transition">
                          Exit
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>

      </div>

      <!-- Column 4: Order Entry Ticket (Right Terminal) -->
      <div class="bg-slate-950/90 rounded-3xl border border-slate-800 p-6 space-y-5 shadow-2xl h-fit sticky top-24">
        
        <div class="border-b border-slate-800 pb-3">
          <span class="text-[9px] uppercase font-bold text-slate-500 tracking-wider font-mono block">ORDER TERMINAL</span>
          <h3 class="text-base font-black text-white font-mono"><?php echo htmlspecialchars($active_contract['ticker']); ?></h3>
        </div>

        <form id="trade-form" class="space-y-4">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <input type="hidden" name="action" value="place_order">
          <input type="hidden" name="contract_id" value="<?php echo $active_contract['id']; ?>">

          <!-- Direction Toggle: BUY (Long) vs SELL (Short) -->
          <div class="grid grid-cols-2 gap-2 p-1 rounded-2xl bg-slate-900 border border-slate-800">
            <label class="cursor-pointer">
              <input type="radio" name="direction" value="BUY" checked class="hidden peer">
              <div class="py-2 text-center text-xs font-black rounded-xl text-slate-400 peer-checked:bg-emerald-500 peer-checked:text-slate-950 transition">
                BUY (Long)
              </div>
            </label>
            <label class="cursor-pointer">
              <input type="radio" name="direction" value="SELL" class="hidden peer">
              <div class="py-2 text-center text-xs font-black rounded-xl text-slate-400 peer-checked:bg-red-500 peer-checked:text-white transition">
                SELL (Short)
              </div>
            </label>
          </div>

          <!-- Order Type -->
          <div>
            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Order Type</label>
            <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
              <label class="flex items-center gap-1.5 p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 cursor-pointer">
                <input type="radio" name="order_type" value="MARKET" checked class="text-orange-500">
                <span>Market Price</span>
              </label>
              <label class="flex items-center gap-1.5 p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 cursor-pointer">
                <input type="radio" name="order_type" value="LIMIT" class="text-orange-500">
                <span>Limit Order</span>
              </label>
            </div>
          </div>

          <!-- Lots & Quantity -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-[10px] uppercase font-bold text-slate-400">Number of Lots</label>
              <span class="text-[10px] text-slate-500 font-mono">1 Lot = <?php echo $active_contract['lot_size']; ?> Contracts</span>
            </div>
            <select id="lots-select" name="lots" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white font-mono font-bold text-xs focus:border-orange-500 focus:outline-none">
              <option value="1">1 Lot (<?php echo $active_contract['lot_size'] * 1; ?> contracts)</option>
              <option value="2">2 Lots (<?php echo $active_contract['lot_size'] * 2; ?> contracts)</option>
              <option value="5">5 Lots (<?php echo $active_contract['lot_size'] * 5; ?> contracts)</option>
              <option value="10">10 Lots (<?php echo $active_contract['lot_size'] * 10; ?> contracts)</option>
            </select>
          </div>

          <!-- Price & Margin Calculation Card -->
          <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2 text-xs font-mono">
            <div class="flex justify-between text-slate-400">
              <span>Contract Price:</span>
              <span class="text-white font-bold">₹<?php echo number_format($active_contract['current_price'], 2); ?></span>
            </div>
            <div class="flex justify-between text-slate-400">
              <span>Total Quantity:</span>
              <span class="text-white font-bold" id="calc-qty"><?php echo $active_contract['lot_size']; ?></span>
            </div>
            <div class="pt-2 border-t border-slate-800 flex justify-between font-bold">
              <span class="text-slate-300">Required Margin:</span>
              <span class="text-emerald-400 font-black text-sm" id="calc-margin">
                ₹<?php echo number_format($active_contract['current_price'] * $active_contract['lot_size'], 2); ?>
              </span>
            </div>
          </div>

          <!-- Submit Order Button -->
          <button type="submit" id="btn-submit-order" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-black text-sm rounded-2xl shadow-xl shadow-emerald-500/25 transition">
            Execute Trade Order →
          </button>
        </form>

        <div class="text-[11px] text-slate-500 text-center">
          Positions are settled upon KIYG 2026 Finals completion based on athlete podium placement.
        </div>

      </div>

    </div>

  </div>
</main>

<script>
const contractPrice = <?php echo (float)$active_contract['current_price']; ?>;
const lotSize = <?php echo (int)$active_contract['lot_size']; ?>;
const lotsSelect = document.getElementById('lots-select');
const calcQty = document.getElementById('calc-qty');
const calcMargin = document.getElementById('calc-margin');
const form = document.getElementById('trade-form');
const btnSubmit = document.getElementById('btn-submit-order');

// Dynamic Margin Calculation
if (lotsSelect) {
  lotsSelect.addEventListener('change', () => {
    const lots = parseInt(lotsSelect.value, 10) || 1;
    const totalQ = lots * lotSize;
    const margin = totalQ * contractPrice;
    if (calcQty) calcQty.textContent = totalQ;
    if (calcMargin) calcMargin.textContent = '₹' + margin.toLocaleString('en-IN', { minimumFractionDigits: 2 });
  });
}

// Order Submission
if (form) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (btnSubmit) {
      btnSubmit.disabled = true;
      btnSubmit.textContent = 'Executing on Exchange...';
    }

    const formData = new FormData(form);
    try {
      const res = await fetch('trade_execute.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.status === 'success') {
        alert(data.message);
        window.location.reload();
      } else {
        alert('Order Notice: ' + data.message);
        if (btnSubmit) {
          btnSubmit.disabled = false;
          btnSubmit.textContent = 'Execute Trade Order →';
        }
      }
    } catch (err) {
      alert('Network or processing error during execution.');
      if (btnSubmit) {
        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Execute Trade Order →';
      }
    }
  });
}

// Square off open position
async function squareOff(tradeId) {
  if (!confirm('Are you sure you want to square off this contract position?')) return;
  const formData = new FormData();
  formData.append('action', 'square_off');
  formData.append('trade_id', tradeId);
  formData.append('csrf_token', '<?php echo htmlspecialchars($csrf_token); ?>');

  try {
    const res = await fetch('trade_execute.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      window.location.reload();
    } else {
      alert('Square-off notice: ' + data.message);
    }
  } catch (err) {
    alert('Square-off execution failed.');
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

