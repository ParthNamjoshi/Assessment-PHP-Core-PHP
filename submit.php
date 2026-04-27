<?php
require_once 'config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data
    $name = trim($_POST['name']);
    $age_group = $_POST['age_group'];
    $rating = (int)$_POST['rating'];
    $feedback = trim($_POST['feedback']);
    
    // Server-side validation
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required";
    } elseif (strlen($name) > 100) {
        $errors[] = "Name must be less than 100 characters";
    }
    
    // Validate age group
    $valid_age_groups = ['18-25', '26-35', '36-45', '46+'];
    if (!in_array($age_group, $valid_age_groups)) {
        $errors[] = "Invalid age group selected";
    }
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5";
    }
    
    // If there are errors, redirect back
    if (!empty($errors)) {
        $error_message = implode(', ', $errors);
        header("Location: index.php?error=" . urlencode($error_message));
        exit();
    }
    
    // Get database connection
    $conn = getDBConnection();
    
    // Prepare SQL statement (prevents SQL injection)
    $stmt = $conn->prepare("INSERT INTO surveys (name, age_group, rating, feedback) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $age_group, $rating, $feedback);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Success - redirect with success message
        $stmt->close();
        $conn->close();
        header("Location: index.php?success=1");
        exit();
    } else {
        // Error - redirect with error message
        $stmt->close();
        $conn->close();
        header("Location: index.php?error=" . urlencode("Database error occurred"));
        exit();
    }
    
} else {
    // If accessed directly, redirect to form
    header("Location: index.php");
    exit();
}
?>