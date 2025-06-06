<?php
$host = 'localhost'; 
$user = "root";      
$pass = "";          
$dbname = "listify"; 

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$tel = $_POST['tel'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if the email already exists
$check_email_sql= "SELECT * FROM userinfo WHERE email='$email'";
$result = $conn->query($check_email_sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($result->num_rows > 0) {
    include 'php_pages/emailexist.html';
    exit();
}

//Insert data into the database
$sql ="INSERT INTO userinfo (fname, lname,tel,email,password) Values ('$fname', '$lname','$tel','$email','$hashed_password')";
if ($conn->query($sql) === TRUE) {
    include 'php_pages/succSignup.html';
    exit();
} 
else {
    echo " Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>