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

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT products.*, userinfo.fname AS seller_fname, userinfo.lname AS seller_lname
        FROM products
        JOIN userinfo ON products.seller_id =userinfo.id
        WHERE products.product_id = ?";

$stmt = $conn->prepare($sql);
$stmt ->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?></title>
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
    </div>>
  <div class = "container">
    <h2>Checkout</h2>
    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
    <form action = "payment.php" method= 'POST'>
      <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
      <input type="hidden" name="seller_id" value="<?php echo $product['seller_id']; ?>">
      <p><strong>Product:</strong> <?php echo htmlspecialchars($product['title']); ?></p>
      <p><strong>Price:</strong> R<?php echo htmlspecialchars($product['price']); ?></p>
      <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['cond']); ?></p>
      <p><strong>City:</strong> <?php echo htmlspecialchars($product['city']); ?></p>
      <p><strong>Seller:</strong> <?php echo htmlspecialchars($product['seller_fname'] . ' ' . $product['seller_lname']); ?></p>
      <p><strong>Quantity:</strong> <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($product['stock_quan']); ?>" required></p>
      <h3>Shipping Information</h3>
      <label for="fname">First Name:</label>
      <input type ="text" id="fname" name="fname" value ="<?php echo htmlspecialchars($_SESSION['fname']); ?>" required>
      <label for="lname">Last Name:</label>
      <input type ="text" id="lname" name="lname" value ="<?php echo htmlspecialchars($_SESSION['lname']); ?>" required>
      <label for ="street name">Street Name:</label>
      <input type ="text" id="street_name" name="street_name" required>
      <label for ="street number">Street Number:</label>
      <input type ="text" id="street_number" name="street_number" required>
      <label for ="suburb">Suburb:</label>
      <input type ="text" id="suburb" name="suburb" required>
      <label for ="city">City:</label>
      <input type ="text" id="city" name="city" required>
      <label for ="province">Province:</label>
      <select for="province" id="province" name="province" required>
        <option value="" disabled selected>Select Province</option>
        <option value ="Easten Cape">Easten Cape</option>
        <option value ="Free State">Free State</option>
        <option value ="Gauteng">Gauteng</option>
        <option value ="KwaZulu-Natal">KwaZulu-Natal</option>
        <option value ="Limpopo">Limpopo</option>
        <option value ="Mpumalanga">Mpumalanga</option>
        <option value ="North West">North West</option>
        <option value ="Northern Cape">Northern Cape</option>
        <option value ="Western Cape">Western Cape</option>
      </select>
      <label for ="postal code">Postal Code:</label>
      <input type ="text" id="postal code" name="postal code" required>
      <label for ="phone number">Phone Number:</label>
      <input type ="text" id="phone number" name="phone number" required>
      <button type="submit">Continue to payment</button>
      <button type="button" onclick="location.href='product.php?id=<?php echo htmlspecialchars($product['product_id']);?>'">Back to Product</button>
    </form>
  </div>