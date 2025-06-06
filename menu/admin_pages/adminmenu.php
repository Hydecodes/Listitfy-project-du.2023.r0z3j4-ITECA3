
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Menu</title>
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
      <?php if ($_SESSION['acclvl']==="'Admin'"):?>
        <h2>Welcome to the Admin menu</h2>
        <button type='button' onclick = "location.href='/project/menu/admin_pages/adminOrders.php'">View All Orders</button>
        <button type='button' onclick = "location.href='adminUsers.php'">View All Accounts</button>
        <button type='button' onclick = "location.href='adminProducts.php'">View All Products</button>
        <br>
        <button type='button' onclick ="location.href='/project/home.php'">Back to Home</button>
      <?php else: ?>
        <h3> Admin Access denied </h3>
      <?php endif; ?>
      
    </div>
  </body>
</html>