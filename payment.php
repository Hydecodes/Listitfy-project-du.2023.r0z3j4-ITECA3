<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'listify';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = $_POST['product_id'];
$seller_id = $_POST['seller_id'];
$buyer_id = $_SESSION['id'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$street_name = $_POST['street_name'];
$street_number = $_POST['street_number'];
$suburb = $_POST['suburb'];
$city = $_POST['city'];
$province = $_POST['province'];
$postal_code = $_POST['postal_code'];
$phone_number = $_POST['phone_number'];
$quantity = $_POST['quantity'];

$stmt = $conn->prepare ("Select price, title FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($product_price, $product_title);
$stmt->fetch();
$stmt->close();

if(!$product_price) {
    die("Product not found.");

}

$shipping_fee = 200;
$service_fee =0.03* $product_price* $quantity;
$clean_total=$product_price * $quantity + $shipping_fee;
$total_price = ($product_price * $quantity) + $shipping_fee + $service_fee;

if (isset($_POST['confirm_order'])) {
    $order_stmt = $conn->prepare("INSERT INTO orders (product_id, seller_id, buyer_id, price, quantity,fname, lname, street_name, street_num, suburb, city, province, p_code, phone_number)
    VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $order_stmt->bind_param("iiidisssssssss", $product_id, $seller_id, $buyer_id, $clean_total, $quantity, $fname, $lname, $street_name, $street_number, $suburb, $city, $province, $postal_code, $phone_number);

    $order_stmt->execute();
    $order_id = $conn->insert_id;
    $order_stmt->close();

    $update_stmt = $conn->prepare ("UPDATE products SET stock_quan = stock_quan - ? WHERE product_id = ?");
    $update_stmt->bind_param("ii", $quantity, $product_id);
    $update_stmt->execute();
    $update_stmt->close();

    header("Location: php_pages/succOrder.php?id=$order_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="Ribbon">
      <div class="ribbon-content">
        <div class="ribbon-left">
          <div class = "acount-menu-wrapper" style ="position:relative; display:inline-block;">
            <button type="button" id = "accountbtn">Account</button>
            <div id= 'accountmenu' style="display:none; position:absolute; top:100%; left:0; background-color:white; border: 1px solid #ccc; min-width:180px; z-index:1000;">
              <a href="/project/menu/mylistings.php" style="display:block; padding:10px; text-decoration:none; color:black;">My Listings</a>
              <a href="/project/menu/myorders.php" style="display:block; padding:10px; text-decoration:none; color:black;">My Orders</a>
              <a href="/project/menu/accountinfo.php" style="display:block; padding:10px; text-decoration:none; color:black;">Account Info</a>
              <?php if($_SESSION['acclvl']==="'Admin'"): ?>
                <a href="/project/menu/admin_pages/adminmenu.php" style="display:block; padding:10px; text-decoration:none; color:black;">Admin Menu</a>
              <?php endif;?>
              <a href="/project/menu/logout.php" style="display:block; padding:10px; text-decoration:none; color:black;">Logout</a>
            </div>
          </div>
          <script>
            document.getElementById('accountbtn').onclick = function() {
              var menu = document.getElementById('accountmenu');
              menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
            };
          
            document.addEventListener('click', function(event) {
              var menu = document.getElementById('accountmenu');
              var btn = document.getElementById('accountbtn');
              if (!btn.contains(event.target) && !menu.contains(event.target)) {
                menu.style.display = 'none';
              }
            });
          </script>
            
          <h3>Welcome,
          <?php 
          echo htmlspecialchars($_SESSION['fname']) ;
          ?>
        </div>
        <div class="ribbon-center">
          <img src ="Listify logo.png" alt="Listify Logo" class="logo">
        </div>
      </div>
    </div>
    <div class = 'container'>
      <h2>Payment</h2>
      <p><strong>Product</strong> <?php echo htmlspecialchars($product_title); ?></p>
      <p><strong>Price</strong> R<?php echo htmlspecialchars($product_price); ?></p>
      <p><strong>Quantity</strong> <?php echo htmlspecialchars($quantity); ?></p>
      <p><strong>Shipping Fee</strong> R<?php echo htmlspecialchars($shipping_fee); ?></p>
      <p><strong>Service Fee</strong> R<?php echo htmlspecialchars($service_fee); ?></p>
      <p>______________________________</p>
      <p><strong>Total Price</strong> R<?php echo htmlspecialchars($total_price); ?></p>
      <form method ="post">
        <input type="hidden" name ="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
        <input type="hidden" name ="seller_id" value="<?php echo htmlspecialchars($seller_id); ?>">
        <input type="hidden" name ="buyer_id" value="<?php echo htmlspecialchars($buyer_id); ?>">
        <input type="hidden" name = "fname" value="<?php echo htmlspecialchars($fname); ?>">
        <input type="hidden" name = "lname" value="<?php echo htmlspecialchars($lname); ?>">
        <input type="hidden" name = "street_name" value="<?php echo htmlspecialchars($street_name); ?>">
        <input type="hidden" name = "street_number" value="<?php echo htmlspecialchars($street_number); ?>">
        <input type="hidden" name = "suburb" value="<?php echo htmlspecialchars($suburb); ?>">
        <input type="hidden" name = "city" value="<?php echo htmlspecialchars($city); ?>">
        <input type="hidden" name = "province" value="<?php echo htmlspecialchars($province); ?>">
        <input type="hidden" name = "postal_code" value="<?php echo htmlspecialchars($postal_code); ?>">
        <input type="hidden" name = "phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
        <input type ='hidden' name = "quantity" value="<?php echo htmlspecialchars($quantity); ?>">
        <button type="submit" name="confirm_order">Confirm Order</button>
      </form>
      <button type ="button" onclick="location.href='checkout.php?id= <?php echo htmlspecialchars ($product_id)?>'">Cancel</button>
    </div>