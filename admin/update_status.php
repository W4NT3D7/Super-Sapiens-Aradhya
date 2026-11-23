<?php
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id          = (int)($_POST['id'] ?? 0);
$status      = clean_input($_POST['status'] ?? '');
$trial_venue = clean_input($_POST['trial_venue'] ?? '');
$trial_date  = clean_input($_POST['trial_date'] ?? '');
$csrf_token  = $_POST['csrf_token'] ?? '';

if (!verify_csrf_token($csrf_token)) {
    die("Security token invalid.");
}

if ($id > 0 && !empty($status)) {
    try {
        $stmt = $pdo->prepare("UPDATE athletes SET status = :status, trial_venue = :venue, trial_date = :tdate, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':venue'  => !empty($trial_venue) ? $trial_venue : NULL,
            ':tdate'  => !empty($trial_date) ? $trial_date : NULL,
            ':id'     => $id
        ]);
    } catch (Exception $e) {
        die("Error updating athlete record: " . $e->getMessage());
    }
}

header("Location: view.php?id=" . $id);
exit;

