<?php
require_once "../includes/header.php";
require_once "../config/database.php";

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $sql = "DELETE FROM outward_correspondence WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = trim($_GET["id"]);
        
        if ($stmt->execute()) {
            header("location: index.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    $stmt->close();
} else {
    if (empty(trim($_GET["id"]))) {
        header("location: index.php");
        exit();
    }
}
?>