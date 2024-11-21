<?php
require_once "config/database.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where = '';
$params = [];
$types = '';

if (!empty($start_date) && !empty($end_date)) {
    $where = "WHERE received_date BETWEEN ? AND ?";
    $params = [$start_date, $end_date];
    $types = "ss";
}

$sql = "SELECT * FROM correspondence $where ORDER BY received_date DESC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="correspondence_report.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, ['ID', 'Subject', 'Received Date', 'Received By', 'Received From', 'File Reference', 'Created At']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>