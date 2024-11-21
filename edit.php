<?php
require_once "includes/header.php";
require_once "config/database.php";

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: index.php");
    exit();
}

$id = $_GET["id"];
$subject = $received_by = $received_from = $file_reference = $received_date = "";
$subject_err = $received_by_err = $received_from_err = $file_reference_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["subject"]))) {
        $subject_err = "Please enter a subject.";
    } else {
        $subject = trim($_POST["subject"]);
    }
    
    if (empty(trim($_POST["received_by"]))) {
        $received_by_err = "Please enter who received it.";
    } else {
        $received_by = trim($_POST["received_by"]);
    }
    
    if (empty(trim($_POST["received_from"]))) {
        $received_from_err = "Please enter who sent it.";
    } else {
        $received_from = trim($_POST["received_from"]);
    }
    
    if (empty(trim($_POST["file_reference"]))) {
        $file_reference_err = "Please enter a file reference.";
    } else {
        $file_reference = trim($_POST["file_reference"]);
    }
    
    if (empty($subject_err) && empty($received_by_err) && empty($received_from_err) && empty($file_reference_err)) {
        $sql = "UPDATE correspondence SET subject=?, received_date=?, received_by=?, received_from=?, file_reference=? WHERE id=?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $param_subject, $param_received_date, $param_received_by, $param_received_from, $param_file_reference, $param_id);
            
            $param_subject = $subject;
            $param_received_date = $_POST["received_date"];
            $param_received_by = $received_by;
            $param_received_from = $received_from;
            $param_file_reference = $file_reference;
            $param_id = $id;
            
            if ($stmt->execute()) {
                header("location: index.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
} else {
    $sql = "SELECT * FROM correspondence WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $id;
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $subject = $row["subject"];
                $received_date = $row["received_date"];
                $received_by = $row["received_by"];
                $received_from = $row["received_from"];
                $file_reference = $row["file_reference"];
            } else {
                header("location: index.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
}
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <h2>Edit Record</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $subject; ?>">
                <span class="invalid-feedback"><?php echo $subject_err; ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Received Date</label>
                <input type="date" name="received_date" class="form-control" value="<?php echo $received_date; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Received By</label>
                <input type="text" name="received_by" class="form-control <?php echo (!empty($received_by_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $received_by; ?>">
                <span class="invalid-feedback"><?php echo $received_by_err; ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Received From</label>
                <input type="text" name="received_from" class="form-control <?php echo (!empty($received_from_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $received_from; ?>">
                <span class="invalid-feedback"><?php echo $received_from_err; ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">File Reference</label>
                <input type="text" name="file_reference" class="form-control <?php echo (!empty($file_reference_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $file_reference; ?>">
                <span class="invalid-feedback"><?php echo $file_reference_err; ?></span>
            </div>
            <div class="mb-3">
                <input type="submit" class="btn btn-primary" value="Update">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>