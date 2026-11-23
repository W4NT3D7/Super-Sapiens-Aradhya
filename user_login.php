<?php
require_once __DIR__ . '/config.php';

$error = null;
$redirect = clean_input($_GET['redirect'] ?? 'discover.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = clean_input($_POST['login_input'] ?? '');
    $password    = $_POST['password'] ?? '';
    $redirect    = clean_input($_POST['redirect'] ?? 'discover.php');

    if (!empty($login_input) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :l1 OR username = :l2 LIMIT 1");
            $stmt->execute([':l1' => $login_input, ':l2' => $login_input]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];

                header('Location: ' . ($redirect ?: 'discover.php'));
                exit;
            } else {
                $error = 'Invalid email/username or password.';
            }
        } catch (Exception $e) {
            $error = 'Sign-in error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please provide your account credentials.';
    }
}

$page_title = 'Sign In | Super Sapiens';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-12 sm:py-20 flex-1 flex items-center justify-center px-4">
  <div class="max-w-md w-full bg-slate-950/80 border border-slate-800 backdrop-blur-xl p-8 sm:p-10 rounded-3xl shadow-2xl">
    
    <div class="text-center mb-8">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 text-white font-black text-xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-orange-500/20">
        SS
      </div>
      <h2 class="text-2xl font-black text-white tracking-tight">Sign In to Super Sapiens</h2>
      <p class="text-xs text-slate-400 mt-1">Access your athlete portfolio, trading desk & wallet</p>
    </div>

    <!-- Quick Demo Access Banner -->
    <div class="mb-6 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300">
      <span class="font-bold text-emerald-400 uppercase text-[10px] tracking-wider block mb-1">⚡ Quick Demo Account:</span>
      <div class="flex items-center justify-between text-[11px] font-mono">
        <span>Email: <code>user@kheloindia.com</code></span>
        <span>Pass: <code>password123</code></span>
      </div>
    </div>

    <?php if ($error): ?>
      <div class="p-4 mb-6 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-300 text-xs text-center font-bold">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="user_login.php" class="space-y-4">
      <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Email or Username</label>
        <input type="text" name="login_input" required value="user@kheloindia.com"
          class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-600 focus:border-orange-500 focus:outline-none text-xs font-semibold">
      </div>

      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Password</label>
        <input type="password" name="password" required value="password123"
          class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-600 focus:border-orange-500 focus:outline-none text-xs font-semibold">
      </div>

      <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-sm rounded-xl shadow-lg shadow-orange-500/25 transition">
        Sign In →
      </button>
    </form>

    <div class="mt-6 text-center text-xs text-slate-500">
      New to Super Sapiens? <a href="user_register.php" class="text-orange-400 hover:underline font-bold">Create Account (Free ₹10k Credits)</a>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

