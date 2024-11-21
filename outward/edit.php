<?php
require_once "../includes/header.php";
require_once "../config/database.php";

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: index.php");
    exit();
}

$id = $_GET["id"];
$subject = $addressee = $messenger_name = $file_reference = $date_dispatched = "";
$subject_err = $addressee_err = $messenger_name_err = $file_reference_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["subject"]))) {
        $subject_err = "Please enter a subject.";
    } else {
        $subject = trim($_POST["subject"]);
    }
    
    if (empty(trim($_POST["addressee"]))) {
        $addressee_err = "Please enter the addressee.";
    } else {
        $addressee = trim($_POST["addressee"]);
    }
    
    if (empty(trim($_POST["messenger_name"]))) {
        $messenger_name_err = "Please enter the messenger's name.";
    } else {
        $messenger_name = trim($_POST["messenger_name"]);
    }
    
    if (empty(trim($_POST["file_reference"]))) {
        $file_reference_err = "Please enter a file reference.";
    } else {
        $file_reference = trim($_POST["file_reference"]);
    }
    
    if (empty($subject_err) && empty($addressee_err) && empty($messenger_name_err) && empty($file_reference_err)) {
        $sql = "UPDATE outward_correspondence SET subject=?, date_dispatched=?, file_reference=?, addressee=?, messenger_name=? WHERE id=?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $param_subject, $param_date_dispatched, $param_file_reference, $param_addressee, $param_messenger_name, $param_id);
            
            $param_subject = $subject;
            $param_date_dispatched = $_POST["date_dispatched"];
            $param_file_reference = $file_reference;
            $param_addressee = $addressee;
            $param_messenger_name = $messenger_name;
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
    $sql = "SELECT * FROM outward_correspondence WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $id;
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $subject = $row["subject"];
                $date_dispatched = $row["date_dispatched"];
                $file_reference = $row["file_reference"];
                $addressee = $row["addressee"];
                $messenger_name = $row["messenger_name"];
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
        <h2>Edit Outward Correspondence</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $subject; ?>">
                <span class="invalid-feedback"><?php echo $subject_err; ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Date Dispatched</label>
                <input type="date" name="date_dispatched" class="form-control" value="<?php echo $date_dispatched; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">File Reference</label>
                <input type="text" name="file_reference" class="form-control <?php echo (!empty($file_reference_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $file_reference; ?>">
                <span class="invalid-feedback"><?php echo $file_reference_err; ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Addressee</label>
                <input type="text" name="addressee" class="form-control <?php echo (!empty($addressee_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $addressee; ?>">
                <span class="invalid-feedback"><?php echo $addressee_err; ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Messenger's Name</label>
                <input type="text" name="messenger_name" class="form-control <?php echo (!empty($messenger_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $messenger_name; ?>">
                <span class="invalid-feedback"><?php echo $messenger_name_err; ?></span>
            </div>
            <div class="mb-3">
                <input type="submit" class="btn btn-primary" value="Update">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>