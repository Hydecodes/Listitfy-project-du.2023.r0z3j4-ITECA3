<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'listify');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $stmt = $conn->prepare("UPDATE userinfo SET fname=?, lname=?, email=?, tel=? WHERE id=?");
    $stmt->bind_param("ssssi", $fname, $lname, $email, $tel, $user_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['fname'] = $fname; 
    $success = "Account details updated successfully!";
}

$stmt = $conn->prepare("SELECT fname, lname, email, tel FROM userinfo WHERE id=?");
$stmt -> bind_param('i', $user_id);
$stmt ->execute();
$stmt->bind_result($fname, $lname, $email, $tel);
$stmt ->fetch();
$stmt -> close();

if ($_SERVER['REQUEST_METHOD'] ==='POST' && isset($_POST['change_password'])) {
  $current_password = $_POST['current_password'];
  $new_password= $_POST['new_password'];
  $confirm_password= $_POST['confirm_password'];

  $stmt =$conn->prepare("SELECT password FROM userinfo WHERE id=?");
  $stmt ->bind_param('i',$user_id);
  $stmt -> execute();
  $stmt ->bind_result($hashed_password);
  $stmt ->fetch();
  $stmt -> close();

  if(!password_verify($current_password, $hashed_password)){
    $error = "Current password is incorrect.";
    }
  elseif($new_password !== $confirm_password){
    $error = 'New passwords do not match';
  }
  elseif (strlen($new_password)<8) {
    $error = 'New password must be atleast 8 characters long';
  }
  else {
    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE userinfo SET password =? WHERE id=?");
    $stmt ->bind_param('si' ,$new_hashed, $user_id);
    $stmt ->execute();
    $stmt -> close();
    $success = "Password updated successfully!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Info</title>
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
      <h1>My Account Details</h1>
      <?php if (!empty($success)):?>
        <div class="success-msg" style="color:green;text-align:center;"><?php echo $success; ?></div>
      <?php endif;?>
      <form class='account-form' method='post'>
        <label for="fname">First Name:</label>
        <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($fname); ?>" required>
          <label for="lname">Last Name:</label>
          <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($lname); ?>" required>
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
          <label for="tel">Phone Number:</label>
          <input type="text" id="tel" name="tel" value="<?php echo htmlspecialchars($tel); ?>" required>
          <button type="submit" name="update_account">Update Details</button>
          <button type="button" name="back_to_home" onclick = "location.href='/project/home.php'">Back to Home</button>
      </form>
    </div>
    <div class = 'container'>
      <h2>Change Password</h2>
      <?php if (!empty($error)): ?>
        <div class="error-msg" style="color:red; text-align:center;"><?php echo $error; ?></div>
      <?php endif;?>
      <?php if(!empty($success)):?>
        <div class="success-msg" style="color:green;text-align:center;"><?php echo $success; ?></div>
      <?php endif; ?>
      <form method="post" class="account-form">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" id="current_password" required>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required
        pattern=".*[^a-zA-Z0-9].*" title="Password must contain atleast 1 special character." placeholder="Password">
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <button type="submit" name="change_password">Change Password</button>
    </form>
  </div>
</body>
</html>

