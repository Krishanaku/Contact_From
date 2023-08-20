<?php
// Connect to your MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Form data
$full_name = $_POST['full_name'];
$phone_number = $_POST['phone_number'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$ip_address = $_SERVER['REMOTE_ADDR']; // Get user's IP address
$timestamp = date("Y-m-d H:i:s"); // Current timestamp


// Form validation
$errors = array();

if (empty($full_name)) {
    $errors[] = "Full Name is required.";
}

if (empty($phone_number)) {
    $errors[] = "Phone Number is required.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (empty($subject)) {
    $errors[] = "Subject is required.";
}

if (empty($message)) {
    $errors[] = "Message is required.";
}

// Check for errors
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    mysqli_close($conn);
    exit();
}


// Check for duplicate submission
$check_query = "SELECT id FROM contact_form WHERE email='$email'";
$result = mysqli_query($conn, $check_query);
if (mysqli_num_rows($result) > 0) {
    echo "Duplicate Data.";
    mysqli_close($conn);
    exit();
}

// Insert data into database
$insert_query = "INSERT INTO contact_form (full_name, phone_number, email, subject, message, ip_address, timestamp) VALUES ('$full_name', '$phone_number', '$email', '$subject', '$message', '$ip_address', '$timestamp')";
if (mysqli_query($conn, $insert_query)) {
    // Send email notification
    $to = "curvahacker8002617@gmail.com"; // Change this to the owner's email address
    $subject = "New Form Submission";
    $email_message = "A new form submission has been received:\n\nFull Name: $full_name\nPhone Number: $phone_number\nEmail: $email\nSubject: $subject\nMessage: $message\nIP Address: $ip_address\nTimestamp: $timestamp";
    mail($to, $subject, $email_message);

    // Success message
    echo "Form submitted successfully! You will receive a confirmation email shortly.";
} else {
    // Error message
    echo "Error: " . $insert_query . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>