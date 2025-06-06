<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'listify');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$buyer_id = $_SESSION['id'];
$sql = "SELECT orders.*, products.title, products.images
        FROM orders
        JOIN products ON orders.product_id = products.product_id
        WHERE orders.buyer_id=?
        ORDER BY orders.order_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
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
          <img src ="/project/Listify logo.png" alt="Listify Logo" class="logo">
        </div>
      </div>
    </div>
  
  <div class = 'container'>
    <h1>My Orders</h1>
    <h3>These are all the orders made by you</h3>
    <p>Please note that some products can take up to 2 weeks to ship</p>
    <table class='orders-table'>
      <tr>
        <th>Order ID</th>
        <th>Product</th>
        
        <th>Quantity</th>
        <th>Order Date</th>
        <th>Total Price</th>
        <th>Status</th>
      </tr>
      <?php while ($order = $result->fetch_assoc()):?>
      <tr>
        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
        <td><?php echo htmlspecialchars($order['title']); ?></td>
        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
        <td><?php echo htmlspecialchars($order['time']); ?></td>
        <td><?php echo htmlspecialchars(number_format($order['price'],2));?></td>
        <td> 
          <?php
            if (isset($order['status'])&& $order['status']==='Shipped') {
              echo "<span class ='shipped'>Shipped</span>";
            } else {
              echo "<span class = 'not-shipped'>Not Shipped yet</span>";
            }
          ?>
        </td>
      </tr>
      <?php endwhile;?>
    </table>
    <button type = "button" onclick="location.href='/project/home.php'">Back to Home</button>
  </div>
</body>
</html>
<?php 
  $stmt->close();
  $conn->close();
?>
        