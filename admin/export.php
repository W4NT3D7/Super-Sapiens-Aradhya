<?php
/**
 * Khelo India School Games (KISG) / KIYG 2026 Portal
 * Ultra-Fast Streaming CSV Exporter for Federation Officials
 * 
 * Streams records row-by-row to prevent PHP memory limits even with 100,000+ applicants.
 */

require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Read filter parameters
$filter_sport  = clean_input($_GET['sport'] ?? '');
$filter_state  = clean_input($_GET['state'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');
$filter_cat    = clean_input($_GET['age_category'] ?? '');

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

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$filename = 'khelo_india_athletes_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// UTF-8 BOM for Microsoft Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Header
fputcsv($output, [
    'Application ID',
    'Full Name',
    'DOB',
    'Gender',
    'Category',
    'Parent/Guardian Name',
    'Mobile Phone',
    'Email Address',
    'State',
    'District',
    'School / Institution',
    'Affiliated Board',
    'Age Division',
    'Sport Discipline',
    'Sub-Event / Category',
    'Playing Experience (Yrs)',
    'Qualifying Tier',
    'Achievements / Medals',
    'Coach Name',
    'Coach Phone',
    'Certificate Ref',
    'Current Status',
    'Trial Assessment Venue',
    'Trial Date',
    'Registered At'
]);

// Stream Query
$sql = "SELECT * FROM athletes {$where_sql} ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['application_id'],
        $row['full_name'],
        $row['dob'],
        $row['gender'],
        $row['category'],
        $row['guardian_name'],
        $row['phone'],
        $row['email'],
        $row['state'],
        $row['district'],
        $row['school_name'],
        $row['board'],
        $row['age_category'],
        $row['sport_discipline'],
        $row['sub_event'],
        $row['playing_experience'],
        $row['qualification_level'],
        $row['achievement_details'],
        $row['coach_name'],
        $row['coach_phone'],
        $row['certificate_no'],
        $row['status'],
        $row['trial_venue'],
        $row['trial_date'],
        $row['created_at']
    ]);
}

fclose($output);
exit;

