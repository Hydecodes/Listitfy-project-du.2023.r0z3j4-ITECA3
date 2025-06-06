<?php
session_start();
 $conn = new mysqli('localhost','root','','listify');
 if ($conn->connect_error){die('Connection failed: ' .$conn->connect_error);}

 if (!isset($_SESSION['acclvl']) || ($_SESSION['acclvl']) !== "'Admin'") {
    echo "<h3>Admin Access denied</h3>";
    exit;
}

$sql = 'SELECT * FROM products ORDER BY products.product_id DESC';
$result=$conn->query($sql);

if($_SERVER['REQUEST_METHOD']==='POST'&& isset($_POST['delete_product'])) {
  $delete_id = intval($_POST['product_id']);
  $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
  $stmt ->bind_param("i",$delete_id);
  $stmt ->execute();
  $stmt ->close();
  header("Location: adminProducts.php");
  exit;
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products</title>
  <link rel="stylesheet" href="/project/style.css">
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
    <div class='container'>
      <h2>Manage Products</h2>
      <table class='orders-table'>
       <tr>
        <th>Product ID</th>
        <th>Seller ID</th>
        <th>Title</th>
        <th>Price</th>
        <th>Condition</th>
        <th>Stock</th>
        <th>Tags</th>
        <th>Time Created</th>
        <th>City</th>
        <th>Delete Product</th>
      </tr>
      <?php while ($product =$result->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($product['product_id']); ?></td>
          <td><?php echo htmlspecialchars($product['seller_id']); ?></td>
          <td>0<?php echo htmlspecialchars($product['title']); ?></td>
          <td>R <?php echo htmlspecialchars($product['price']); ?></td>
          <td><?php echo htmlspecialchars($product['cond']); ?></td>
          <td><?php echo htmlspecialchars($product['stock_quan']); ?></td>
          <td><?php echo htmlspecialchars($product['tags']); ?></td>
          <td><?php echo htmlspecialchars($product['created_at']); ?></td>
          <td><?php echo htmlspecialchars($product['city']); ?></td>
          <td>
            <form method='post'>
              <input type='hidden' name='product_id' value='<?php echo $product['product_id'];?>'>
              <button type='submit' name='delete_product' style='background-color:red'>Delete Product</button>
            </form>
          </td>
        </tr>
      <?php endwhile;?>
      </table> 
      <button type='button' onclick = "location.href='adminmenu.php'">Back to Admin Menu</button>              
    </div>
</body>
</html>

