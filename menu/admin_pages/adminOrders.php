<?php
session_start();
 $conn = new mysqli('localhost','root','','listify');
 if ($conn->connect_error){die('Connection failed: ' .$conn->connect_error);}

 if (!isset($_SESSION['acclvl']) || trim($_SESSION['acclvl'], "'") !== 'Admin') {
    echo "<h3>Admin Access denied</h3>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $delete_id = intval($_POST['order_id']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: adminOrders.php");
    exit;
}
$sql ="SELECT orders.*, products.product_id, products.title AS product_title 
FROM orders 
JOIN products ON orders.product_id = products.product_id
ORDER BY orders.order_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Orders</title>
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
    <div class='container'>
      <h2>All Orders</h2>
      <table class='orders-table'>
       <tr>
        <th>Order ID</th>
        <th>Product ID</th>
        <th>Seller ID</th>
        <th>Buyer ID</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Buyer Name</th>
        <th>Street</th>
        <th>Suburb</th>
        <th>City</th>
        <th>Province</th>
        <th>Postal code</th>
        <th>Phone Number</th>
        <th>Status</th>
        <th>Time</th>
        <th>Delete Order</th>
      </tr>
      <?php while ($order =$result->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($order['order_id']); ?></td>
          <td><?php echo htmlspecialchars($order['product_id']); ?></td>
          <td><?php echo htmlspecialchars($order['seller_id']); ?></td>
          <td><?php echo htmlspecialchars($order['buyer_id']); ?></td>
          <td><?php echo htmlspecialchars($order['price']); ?></td>
          <td><?php echo htmlspecialchars($order['quantity']); ?></td>
          <td><?php echo htmlspecialchars($order['fname'].' '.$order['lname']); ?></td>
          <td><?php echo htmlspecialchars($order['street_num'].' '.$order['street_name']); ?></td>
          <td><?php echo htmlspecialchars($order['suburb']); ?></td>
          <td><?php echo htmlspecialchars($order['city']); ?></td>
          <td><?php echo htmlspecialchars($order['province']); ?></td>
          <td><?php echo htmlspecialchars($order['p_code']); ?></td>
          <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
          <td><?php echo htmlspecialchars($order['status']); ?></td>
          <td><?php echo htmlspecialchars($order['time']); ?></td>
          <td>
            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this order?');">
              <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
              <button type="submit" name="delete_order" style="background-color:red;">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile;?>
      </table> 
      <button type='button' onclick = "location.href='adminmenu.php'">Back to Admin Menu</button>              
    </div>
</body>
</html>

