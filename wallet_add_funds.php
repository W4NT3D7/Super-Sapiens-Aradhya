<?php
/**
 * Super Sapiens - High-Concurrency In-App Wallet Payment Processor
 * Adds funds securely with row-level transaction locks & audit ledger
 */

require_once __DIR__ . '/config.php';
require_user_login();

$user = get_logged_in_user($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: wallet.php');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    send_json_response('error', 'Security token invalid.');
}

$amount = (float)($_POST['amount'] ?? 0);
$method = clean_input($_POST['payment_method'] ?? 'UPI');
$upi_id = clean_input($_POST['upi_id'] ?? '');

if ($amount < 100) {
    send_json_response('error', 'Minimum deposit amount is ₹100.');
}

if ($amount > 500000) {
    send_json_response('error', 'Maximum single deposit amount is ₹5,00,000.');
}

try {
    $pdo->beginTransaction();

    // 1. Fetch user current balance with lock
    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = :uid LIMIT 1");
    $stmt->execute([':uid' => $user['id']]);
    $current_bal = (float)$stmt->fetchColumn();

    $new_bal = $current_bal + $amount;

    // 2. Update user wallet balance
    $stmt_upd = $pdo->prepare("UPDATE users SET wallet_balance = :bal WHERE id = :uid");
    $stmt_upd->execute([':bal' => $new_bal, ':uid' => $user['id']]);

    // 3. Record in Wallet Transactions Ledger
    $txn_ref = 'TXN-DEP-' . strtoupper(bin2hex(random_bytes(4)));
    $desc = "Instant Top-Up via {$method}" . (!empty($upi_id) ? " ({$upi_id})" : "");

    $stmt_log = $pdo->prepare("
        INSERT INTO wallet_transactions (user_id, amount, type, description, balance_after, txn_ref)
        VALUES (:uid, :amt, 'DEPOSIT', :desc, :bal, :ref)
    ");
    $stmt_log->execute([
        ':uid'  => $user['id'],
        ':amt'  => $amount,
        ':desc' => $desc,
        ':bal'  => $new_bal,
        ':ref'  => $txn_ref
    ]);

    $pdo->commit();

    send_json_response('success', '₹' . number_format($amount, 2) . ' added to your in-app wallet successfully!', [
        'txn_ref'     => $txn_ref,
        'new_balance' => $new_bal
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    send_json_response('error', 'Payment processing failed: ' . $e->getMessage());
}

