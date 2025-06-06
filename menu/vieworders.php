<?php
session_start();
$conn = new mysqli('localhost', 'root','', 'listify');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$seller_id = $_SESSION['id'];

if (isset($_POST['mark_shipped']) && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $stmt = $conn -> prepare("UPDATE orders SET status = 'Shipped' WHERE order_id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $order_id, $seller_id);
    $stmt->execute();
    $stmt->close();
    
}

$sql = "SELECT orders.*, products.title FROM orders JOIN products ON orders.product_id = products.product_id WHERE orders.seller_id =? ORDER BY orders.order_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
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
      <h1>Orders for your products</h1>
      <table class="orders-table">
        <tr>
          <th>Order ID</th>
          <th>Product Title</th>
          <th>Buyer Name</th>
          <th>Quantity</th>
          <th>street Name</th>
          <th>Street Number</th>
          <th>Suburb</th>
          <th>City</th>
          <th>Province</th>
          <th>Postcode</th>
          <th>Phone Numeber</th>
          <th>Order Date</th>
          <th>Total Price</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php while ($order = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
            <td><?php echo htmlspecialchars($order['title']); ?></td>
            <td><?php echo htmlspecialchars($order['fname'] . ' ' . $order['lname']); ?></td>
            <td><?php echo htmlspecialchars($order['quantity']); ?></td>
            <td><?php echo htmlspecialchars($order['street_name']); ?></td>
            <td><?php echo htmlspecialchars($order['street_num']); ?></td>
            <td><?php echo htmlspecialchars($order['suburb']); ?></td>
            <td><?php echo htmlspecialchars($order['city']); ?></td>
            <td><?php echo htmlspecialchars($order['province']); ?></td>
            <td><?php echo htmlspecialchars($order['p_code']); ?></td>
            <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
            <td><?php echo htmlspecialchars($order['time']); ?></td>
            <td>R<?php echo htmlspecialchars(number_format($order['price'], 2)); ?></td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td>
              <?php if ($order['status'] !== 'Shipped'): ?>
                <form method="post" style="margin:0;">
                  <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                  <button type="submit" name="mark_shipped">Mark as Shipped</button>
                </form>
              <?php else: ?>
                <button type="button" disabled>Shipped</button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
      <button type="button" onclick="location.href='mylistings.php'">Back to Listings</button>
    </div>
  </body>
</html>
<?php
$stmt->close();
$conn->close();
?>
