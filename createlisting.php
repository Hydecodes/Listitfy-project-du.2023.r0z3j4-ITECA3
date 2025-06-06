<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = "root";
$pass = '';
$dbname = "listify";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = trim($_POST['title']);
$description = trim($_POST['description']);
$price = floatval(trim($_POST['price']));
$cond = trim($_POST['cond']);
$stock_quan = intval(trim($_POST['stock_quan']));
$tags = trim($_POST['tags']);
$city = trim($_POST['city']);


$image_path = [];
$upload_dir = 'uploads/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (!empty($_FILES['images']['name'][0])){
  foreach ($_FILES['images']['tmp_name'] as $key => $temp_name) {
    $file_name = basename($_FILES['images']['name'][$key]);
    $target_file = $upload_dir.uniqid()."_".$file_name;
    if (move_uploaded_file($temp_name, $target_file)) {
        $image_path[] = $target_file;
    } else {
        echo "Error uploading file: " . $_FILES['images']['name'][$key];
    }
  }
}

$images_json = json_encode($image_path);
$sellor_id=$_SESSION['id'];


$sql = "INSERT INTO products (title, description, price, cond, stock_quan, tags, city, images, seller_id) Values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdsisssi", $title, $description, $price, $cond, $stock_quan, $tags, $city, $images_json, $sellor_id);
if ($stmt->execute()) {
    include 'php_pages/succlisting.html';
    $stmt->close();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}