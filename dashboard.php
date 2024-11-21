<?php
require_once "includes/header.php";
require_once "config/database.php";

// Get total records
$sql = "SELECT COUNT(*) as total FROM correspondence";
$result = $conn->query($sql);
$total_records = $result->fetch_assoc()['total'];

// Get today's records
$sql = "SELECT COUNT(*) as today FROM correspondence WHERE DATE(created_at) = CURDATE()";
$result = $conn->query($sql);
$today_records = $result->fetch_assoc()['today'];

// Get records by month
$sql = "SELECT MONTH(received_date) as month, COUNT(*) as count 
        FROM correspondence 
        WHERE YEAR(received_date) = YEAR(CURDATE())
        GROUP BY MONTH(received_date)";
$monthly_stats = $conn->query($sql);

// Get recent records
$sql = "SELECT * FROM correspondence ORDER BY created_at DESC LIMIT 5";
$recent_records = $conn->query($sql);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2>Dashboard</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Statistics</h5>
                <p>Total Records: <?php echo $total_records; ?></p>
                <p>Records Today: <?php echo $today_records; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Monthly Distribution</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Records</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $monthly_stats->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date("F", mktime(0, 0, 0, $row['month'], 1)); ?></td>
                                <td><?php echo $row['count']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Records</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Received Date</th>
                                <th>Received By</th>
                                <th>File Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $recent_records->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo htmlspecialchars($row['received_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['received_by']); ?></td>
                                <td><?php echo htmlspecialchars($row['file_reference']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>