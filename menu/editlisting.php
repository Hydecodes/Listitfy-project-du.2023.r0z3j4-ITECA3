<?php

session_start();
$conn = new mysqli('localhost', 'root', '', 'listify');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_listing'])) {
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Listing deleted!'); window.location.href='mylistings.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];
    $cond = $_POST['cond'];
    $city = $_POST['city'];
    $stock_quan = $_POST['stock_quan'];
    

    $stmt = $conn->prepare("UPDATE products SET title=?, price=?, description=?, tags=?,  cond=?, city=? , stock_quan=? WHERE product_id=?");
    $stmt->bind_param("sdssssii", $title, $price, $description, $tags,  $cond, $city, $stock_quan, $product_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Listing updated!'); window.location.href='mylistings.php';</script>";
    exit;
}

// Load product data
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) { die("Listing not found."); }


?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Listing</title></head>
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
      <h2>Edit Listing</h2>
      <form method="post">
        <label>Title: <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required></label><br>
        <label>Price: <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required></label><br>
        <label>Description: <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea></label><br>
        <label>Tags: <input type="text" name="tags" value="<?php echo htmlspecialchars($product['tags']); ?>" required></label><br>
            <label for ='cond'>Condition</label>
            <select id="cond" name="cond" required>
                <option value="" disabled selected>Select condition</option>
                <option value="new">New</option>
                <option value="Used - Good">Used - Good</option>
                <option value="Used - Fair">Used - Fair</option>
                <option value="Used - Poor">Used - Poor</option>
            </select>
        </label><br>
        <label>City: <input type="text" name="city" value="<?php echo htmlspecialchars($product['city']); ?>" required></label><br>
        <label>Stock Quantity: <input type="number" name="stock_quan" value="<?php echo htmlspecialchars($product['stock_quan']); ?>" required></label><br>
        <button type="submit">Save Changes</button>
      </form>
      <form method="post" onsubmit="return confirm('Are you sure you want to delete this listing?');">
        <button type="submit" name="delete_listing" style="background-color:red; ">Delete Listing</button>
      </form>
      <button type="button" onclick="location.href='mylistings.php'">Back to My Listings</button>
    </div>
</body>
</html>