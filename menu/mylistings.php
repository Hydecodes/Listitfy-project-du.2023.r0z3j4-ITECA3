<?php
session_start();

$user_id = $_SESSION['id'];

$conn = new mysqli('localhost', 'root', '', 'listify');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT products.*, userinfo.fname AS seller_fname, userinfo.lname AS seller_lname
        FROM products
        JOIN userinfo ON products.seller_id = userinfo.id
        WHERE products.seller_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/project/style.css">
  <title>Document</title>
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
    <div class ='container'>
      <h1>My Listings</h1>
      <p> click on the listing you wish to edit</p>

      <div class="listing-cards">
      <?php 
        while ($row = $result -> fetch_assoc()):
        $images = json_decode($row['images'], true);
        $first_image = (!empty($images)) && isset($images[0]) ? htmlspecialchars($images[0]) : 'default_image.jpg';
      ?>
      <a href="editlisting.php?id=<?php echo $row['product_id']; ?>" class="listing-card-link" style="text-decoration:none;color:inherit;"> 
          <div class="listing-card">
            <img src="/project/<?php echo $first_image; ?>" alt="Product Image" class="listing-image">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p>Price: R<?php echo htmlspecialchars($row['price']); ?></p>
            <p>Condition: <?php echo htmlspecialchars($row['cond']); ?></p>
            <p>City: <?php echo htmlspecialchars($row['city']); ?></p>
          </div>
      </a>
      <?php endwhile; ?>
      </div>
      <button type='button' onclick = "location.href='/project/home.php'">Back to Home</button>
      <button type="button" onclick="location.href='/project/menu/vieworders.php'">View Orders Recieved</button>
    </div>

</body>
</html>