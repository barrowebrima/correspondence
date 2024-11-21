<?php
require_once "includes/header.php";
require_once "config/database.php";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if (!empty($search)) {
    $search = "%{$search}%";
    $where = "WHERE subject LIKE ? OR received_by LIKE ? OR received_from LIKE ? OR file_reference LIKE ?";
}

$sql = "SELECT * FROM correspondence $where ORDER BY received_date DESC";
$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $stmt->bind_param("ssss", $search, $search, $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2>Inward Correspondence Records</h2>
    </div>
    <div class="col-md-6">
        <form class="d-flex" method="GET">
            <input class="form-control me-2" type="search" name="search" placeholder="Search records..." value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </div>
</div>

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
                <th>Actions</th>
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
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once "includes/footer.php"; ?>