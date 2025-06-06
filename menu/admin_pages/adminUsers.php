<?php
session_start();
 $conn = new mysqli('localhost','root','','listify');
 if ($conn->connect_error){die('Connection failed: ' .$conn->connect_error);}

 if (!isset($_SESSION['acclvl']) || ($_SESSION['acclvl']) !== "'Admin'") {
    echo "<h3>Admin Access denied</h3>";
    exit;
}

$sql ="SELECT * FROM userinfo
ORDER BY userinfo.id DESC";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_acclvl'])) {
    $target_id = intval($_POST['user_id']);
    $new_acclvl = $_POST['new_acclvl'];
    $stmt = $conn->prepare("UPDATE userinfo SET acclvl=? WHERE id=?");
    $stmt->bind_param("si", $new_acclvl, $target_id);
    $stmt->execute();
    $stmt->close();
   
    header("Location: adminUsers.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $delete_id = intval($_POST['user_id']);
    if ($delete_id != $_SESSION['id']) {
        $stmt = $conn->prepare("DELETE FROM userinfo WHERE id=?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: adminUsers.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Accounts</title>
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
      <h2>Manage Accounts</h2>
      <table class='orders-table'>
       <tr>
        <th>Account ID</th>
        <th>Name</th>
        <th>Tel</th>
        <th>Email</th>
        <th>Access Level</th>
        <th>Action</th>
        <th>Delete Account</th>
      </tr>
      <?php while ($user =$result->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($user['id']); ?></td>
          <td><?php echo htmlspecialchars($user['fname'].' '.$user['lname']); ?></td>
          <td>0<?php echo htmlspecialchars($user['tel']); ?></td>
          <td><?php echo htmlspecialchars($user['email']); ?></td>
          <td><?php echo htmlspecialchars($user['acclvl']); ?></td>
          <td>
            <?php if ($user['acclvl'] !== "'Admin'"): ?>
              <form method="post">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <input type="hidden" name="new_acclvl" value="'Admin'">
                <button type="submit" name="change_acclvl">Make Admin</button>
              </form>
            <?php else: ?>
              <form method="post">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <input type="hidden" name="new_acclvl" value="'User'">
                <button type="submit" name="change_acclvl" style=background-color:red;>Remove Admin</button>
              </form>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($user['id'] != $_SESSION['id']):  ?>
              <form method="post"  onsubmit="return confirm('Are you sure you want to delete this account?');">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <button type="submit" name="delete_user" style="background-color:red;">Delete Account</button>
              </form>
            <?php else: ?>
              <span style="color:gray;">N/A</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile;?>
      </table> 
      <button type='button' onclick = "location.href='adminmenu.php'">Back to Admin Menu</button>              
    </div>
</body>
</html>

