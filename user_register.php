<?php
require_once __DIR__ . '/config.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name  = clean_input($_POST['full_name'] ?? '');
    $username   = clean_input($_POST['username'] ?? '');
    $email      = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone      = clean_input(preg_replace('/\D/', '', $_POST['phone'] ?? ''));
    $password   = $_POST['password'] ?? '';
    $terms      = $_POST['terms_accepted'] ?? '';
    $sports_arr = $_POST['favorite_sports'] ?? ['Athletics'];
    $fav_sports = implode(',', array_map('clean_input', (array)$sports_arr));

    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error = 'All primary fields (Name, Username, Email, Password) are required.';
    } elseif (!$terms) {
        $error = 'You must review and accept the Terms & Conditions to register.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters in length.';
    } else {
        try {
            // Check existing email or username
            $chk = $pdo->prepare("SELECT id FROM users WHERE email = :e OR username = :u LIMIT 1");
            $chk->execute([':e' => $email, ':u' => $username]);
            if ($chk->fetch()) {
                $error = 'An account with this email address or username already exists.';
            } else {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $initial_balance = DEFAULT_SIGNUP_BONUS;

                $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password_hash, phone, wallet_balance, favorite_sports, kyc_status) 
                    VALUES (:name, :user, :email, :pass, :phone, :bal, :sports, 'Verified')");
                $stmt->execute([
                    ':name'   => $full_name,
                    ':user'   => $username,
                    ':email'  => $email,
                    ':pass'   => $password_hash,
                    ':phone'  => $phone ?: NULL,
                    ':bal'    => $initial_balance,
                    ':sports' => $fav_sports
                ]);

                $new_user_id = $pdo->lastInsertId();

                // Add Signup Demo Bonus Transaction
                $txn_ref = 'TXN-INIT-' . mt_rand(100000, 999999);
                $stmt_txn = $pdo->prepare("INSERT INTO wallet_transactions (user_id, amount, type, description, balance_after, txn_ref) 
                    VALUES (?, ?, 'SIGNUP_BONUS', 'Super Sapiens Welcome Demo Trading Credits', ?, ?)");
                $stmt_txn->execute([$new_user_id, $initial_balance, $initial_balance, $txn_ref]);

                // Auto login user
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = $full_name;

                header('Location: discover.php?onboarded=1');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Registration failure: ' . $e->getMessage();
        }
    }
}

$page_title = 'Create User Account & Onboarding | Super Sapiens';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-12 sm:py-16 flex-1 flex items-center justify-center px-4">
  <div class="max-w-xl w-full bg-slate-950/80 border border-slate-800 backdrop-blur-xl p-8 sm:p-10 rounded-3xl shadow-2xl">
    
    <div class="text-center mb-8">
      <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-500/20 text-orange-400 border border-orange-500/30 mb-2">
        Requirement 1: User Onboarding
      </span>
      <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Create Super Sapiens Account</h2>
      <p class="text-xs text-slate-400 mt-1">
        Discover athletes, follow performance news, and get an instant <span class="text-emerald-400 font-bold">₹10,000 Virtual Demo Balance</span>.
      </p>
    </div>

    <?php if ($error): ?>
      <div class="p-4 mb-6 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-300 text-xs text-center font-bold">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="user_register.php" class="space-y-4">
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Full Name *</label>
          <input type="text" name="full_name" required placeholder="e.g. Vikramaditya Roy"
            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-600 focus:border-orange-500 focus:outline-none text-xs font-semibold">
        </div>

        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Username *</label>
          <input type="text" name="username" required placeholder="e.g. vikram_sports"
            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-600 focus:border-orange-500 focus:outline-none text-xs font-semibold">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Email Address *</label>
          <input type="email" name="email" required placeholder="name@example.com"
            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-600 focus:border-orange-500 focus:outline-none text-xs font-semibold">
        </div>

        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Mobile Phone (Optional)</label>
          <input type="tel" name="phone" placeholder="10-digit number"
            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-600 focus:border-orange-500 focus:outline-none text-xs font-semibold">
        </div>
      </div>

      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Account Password *</label>
        <input type="password" name="password" required placeholder="Minimum 6 characters"
          class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-600 focus:border-orange-500 focus:outline-none text-xs font-semibold">
      </div>

      <!-- Favorite Sports for Personalized News Feed (Requirement 5) -->
      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
          Select Your Favorite Sports (Powers your Personalized News Algorithm):
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
          <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-orange-500/50 cursor-pointer text-slate-300">
            <input type="checkbox" name="favorite_sports[]" value="Athletics" checked class="rounded text-orange-500">
            <span>🏃 Athletics</span>
          </label>
          <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-orange-500/50 cursor-pointer text-slate-300">
            <input type="checkbox" name="favorite_sports[]" value="Kalaripayattu" checked class="rounded text-orange-500">
            <span>⚔️ Kalaripayattu</span>
          </label>
          <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-orange-500/50 cursor-pointer text-slate-300">
            <input type="checkbox" name="favorite_sports[]" value="Swimming" checked class="rounded text-orange-500">
            <span>🏊 Swimming</span>
          </label>
          <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-orange-500/50 cursor-pointer text-slate-300">
            <input type="checkbox" name="favorite_sports[]" value="Mallakhamba" class="rounded text-orange-500">
            <span>🚩 Mallakhamba</span>
          </label>
          <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-orange-500/50 cursor-pointer text-slate-300">
            <input type="checkbox" name="favorite_sports[]" value="Gatka" class="rounded text-orange-500">
            <span>🛡️ Gatka</span>
          </label>
          <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-orange-500/50 cursor-pointer text-slate-300">
            <input type="checkbox" name="favorite_sports[]" value="Boxing" class="rounded text-orange-500">
            <span>🥊 Boxing</span>
          </label>
        </div>
      </div>

      <!-- Terms & Conditions Acceptance -->
      <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 text-[11px] text-slate-400">
        <label class="flex items-start gap-2.5 cursor-pointer">
          <input type="checkbox" name="terms_accepted" required class="mt-0.5 rounded text-orange-500 focus:ring-orange-500">
          <span>
            I have read and accept the <strong>Platform Terms & Conditions</strong> and acknowledge that athlete trading contracts operate on a virtual simulation model designed for sporting talent discovery.
          </span>
        </label>
      </div>

      <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-sm rounded-xl shadow-lg shadow-orange-500/25 transition">
        Create Account & Claim ₹10,000 Demo Balance →
      </button>

    </form>

    <div class="mt-6 text-center text-xs text-slate-500">
      Already have an account? <a href="user_login.php" class="text-orange-400 hover:underline font-bold">Sign In here</a>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

