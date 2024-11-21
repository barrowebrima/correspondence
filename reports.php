<?php
require_once "includes/header.php";
require_once "config/database.php";

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
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2>Generate Reports</h2>
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                    <?php if (!empty($start_date) && !empty($end_date)): ?>
                    <button type="button" class="btn btn-success" onclick="window.print()">Print Report</button>
                    <a href="export.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-info">Export CSV</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($start_date) && !empty($end_date)): ?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Received Date</th>
                <th>Received By</th>
                <th>Received From</th>
                <th>File Reference</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['received_date']); ?></td>
                <td><?php echo htmlspecialchars($row['received_by']); ?></td>
                <td><?php echo htmlspecialchars($row['received_from']); ?></td>
                <td><?php echo htmlspecialchars($row['file_reference']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once "includes/footer.php"; ?>