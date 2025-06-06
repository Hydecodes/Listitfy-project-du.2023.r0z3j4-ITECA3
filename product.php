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

$images = [];
if (!empty($product['images'])) {
    $images = json_decode($product['images'], true);
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
    </div>
    <div class="container">
        <h2><?php echo htmlspecialchars($product['title']); ?></h2>
        <div class="product-details">
          <div class="product-images" style="position:relative; width:320px; margin: 0 auto;">
            <?php if (!empty($images)): ?>
              <?php foreach ($images as $idx => $image): ?>
                <img src="/project/<?php echo htmlspecialchars($image); ?>"
                  alt="Product Image"
                  class="product-image"
                  style="display:<?php echo $idx === 0 ? 'block' : 'none'; ?>; width:320px; height:320px; object-fit:cover;">
              <?php endforeach; ?>
              <?php if (count($images) > 1): ?>
                <div class="img-nav-row" style="display:flex; justify-content:center; gap:1rem; margin-top:0.5rem;">
                  <button id="prevBtn" type="button" class="img-nav-btn">&#8592; Prev</button>
                  <button id="nextBtn" type="button" class="img-nav-btn">Next &#8594;</button>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <img src="default_image.jpg" alt="Default Image" class="product-image" style="width:320px; height:320px; object-fit:cover;">
            <?php endif; ?>
          </div>

          <div class="product-info">
              <p><strong>Price:</strong> R <?php echo htmlspecialchars($product['price']); ?></p>
              <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
              <p><strong>Seller:</strong> <?php echo htmlspecialchars($product['seller_fname'] . ' ' . $product['seller_lname']); ?></p>
              <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['cond']); ?></p>
              <p><strong>City:</strong> <?php echo htmlspecialchars($product['city']); ?></p>
              <p><strong>Stock Quantity:</strong> <?php echo htmlspecialchars($product['stock_quan']); ?></p>
              <p><strong>Created At:</strong> <?php echo htmlspecialchars($product['created_at']); ?></p>
              <?php if ($product['stock_quan']>0):?>
                <button type="button" onclick="location.href='checkout.php?id=<?php echo $product['product_id'];?>'">Buy</button>
              <?php else: ?>
                <p style="color: red;">Out of Stock</p>
              <?php endif; ?>
              <button type="button" onclick = "location.href='home.php'">Back to Home</button>
        </div>
    </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.product-images .product-image');
        let current = 0;
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        function showImage(idx) {
            images.forEach((img, i) => {
                img.style.display = (i === idx) ? 'block' : 'none';
            });
        }

        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', function() {
                current = (current - 1 + images.length) % images.length;
                showImage(current);
            });
            nextBtn.addEventListener('click', function() {
                current = (current + 1) % images.length;
                showImage(current);
            });
        }
    });
  </script>
</body>