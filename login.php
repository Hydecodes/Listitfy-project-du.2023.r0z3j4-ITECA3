<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


$host = 'localhost'; 
$user = "root";
$pass = "";
$dbname = "listify"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email= trim($_POST['email']);
$password=trim($_POST['password']);

$sql = "SELECT * FROM userinfo WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();


    if (password_verify($password,$user['password'])) {
        // Set session data
        $_SESSION['email'] = $user['email'];
        $_SESSION['fname'] = $user['fname'];
        $_SESSION['lname'] = $user['lname'];
        $_SESSION['id']= $user['id'];
        $_SESSION['acclvl']= $user['acclvl'];

        include 'php_pages/succLogin.php';
        exit();
    } else {
        include 'php_pages/incoPass.html';
        exit();
    }
} else {
    include 'php_pages/noAcc.html';
    exit();
}

$conn->close();
?>