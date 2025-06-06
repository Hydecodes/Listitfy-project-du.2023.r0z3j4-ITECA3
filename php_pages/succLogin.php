
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="/project/style.css">
</head>
<body>
    <div class="Ribbon">
      <div class="ribbon-content">
        <div class="ribbon-center">
          <img src ="Listify logo.png" alt="Listify Logo" class="logo">
        </div>
      </div>
    </div>

    <div class="container">
      <h2>Login successful!</h2>
      <h3>Welcome, 
        <?php 
          echo htmlspecialchars($_SESSION['fname']) ;
        ?>
      <p>You will be redirected to the home page in 5 seconds...</p>
      <script>
        setTimeout(function() {
          window.location.href = 'home.php';
          }, 5000);
      </script>
    </div>


  
</body>
</html>