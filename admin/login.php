<?php
require_once __DIR__ . '/../config.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_user'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid administrative credentials.';
            }
        } catch (Exception $e) {
            $error = 'Authentication error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sports Authority Login | Khelo India Games Management</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">

  <div class="max-w-md w-full bg-slate-800/90 border border-slate-700 backdrop-blur-xl p-8 rounded-3xl shadow-2xl">
    
    <div class="text-center mb-8">
      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 text-white font-black text-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-500/20">
        KI
      </div>
      <h2 class="text-2xl font-black tracking-tight">Sports Authority Portal</h2>
      <p class="text-xs text-slate-400 mt-1">Khelo India Talent Scouting & Athlete Management</p>
    </div>

    <?php if ($error): ?>
      <div class="p-4 mb-6 rounded-xl bg-red-500/20 border border-red-500/40 text-red-300 text-xs text-center font-semibold">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="space-y-4">
      <div>
        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Username</label>
        <input type="text" id="username" name="username" required value="admin"
          class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
      </div>

      <div>
        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Password</label>
        <input type="password" id="password" name="password" required placeholder="••••••••••••"
          class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
      </div>

      <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-700/60 text-[11px] text-slate-400">
        <span class="font-bold text-amber-400">Default Credentials:</span><br>
        Username: <code>admin</code> • Password: <code>kheloindia@2026</code>
      </div>

      <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/25 transition">
        Sign In to Management Dashboard →
      </button>
    </form>

    <div class="mt-6 text-center">
      <a href="../index.php" class="text-xs text-slate-400 hover:text-white transition">← Return to Public Website</a>
    </div>

  </div>

</body>
</html>

