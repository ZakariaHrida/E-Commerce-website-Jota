<?php
include 'DB.php';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';


if ($searchQuery) {
  $searchParam = "%$searchQuery%";


  $stmt = $pdo->prepare("SELECT * FROM products WHERE 
                          (name LIKE ? OR short_description LIKE ? OR description LIKE ? OR 
                          category LIKE ? OR brand LIKE ? OR reference LIKE ?)");
  $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
  $allResults = $stmt->fetchAll();


  $laptops = array_filter($allResults, function ($item) {
    return $item['category'] === 'Laptop Gamer';
  });
  $pcs = array_filter($allResults, function ($item) {
    return $item['category'] === 'PC Gamer';
  });
  $accessories = array_filter($allResults, function ($item) {
    return $item['category'] === 'Accessories';
  });
} else {

  $laptops = $pdo->query("SELECT * FROM products WHERE category = 'Laptop Gamer'")->fetchAll();
  $pcs = $pdo->query("SELECT * FROM products WHERE category = 'PC Gamer'")->fetchAll();
  $accessories = $pdo->query("SELECT * FROM products WHERE category = 'Accessories'")->fetchAll();
}

function displayProducts($products)
{
  foreach ($products as $product) {
    echo '
        <div class="card">
            <a href="BUY.php?id=' . $product['id'] . '">
                <img src="img/' . $product['image'] . '" alt="' . $product['name'] . '" />
                <h3>' . $product['name'] . '</h3>
                <p>' . $product['short_description'] . '</p>
                <p>PRICE : ' . number_format($product['price'], 2, ',', ' ') . 'dh</p>
            </a>
        </div>';
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="img/icon.png" />
  <title>JOTA</title>
  <style>
    form input[type="text"] {
      background-image: url('img/search-bar.png');
      background-position: 10px center;
      background-repeat: no-repeat;
      background-size: 20px 20px;
      padding-left: 40px;
    }

    form input {
      padding: 10px;
      width: 500px;
      border: 2px solid #ccc;
      border-radius: 25px;
      outline: none;
      transition: all 0.3s ease-in-out;
    }

    form input:focus {
      border-color: #007bff;
      box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
      transform: scale(1.05);
    }

    button {
      background: none;
      border: none;
      cursor: pointer;
      transition: transform 0.3s ease-in-out;
    }

    button:hover {
      transform: scale(1.1);
    }

    a {
      text-decoration: none;
      color: white;
    }

    .card:hover {
      transform: scale(1.05);
      transition: transform 0.5s;
    }

    .search-container {
      display: flex;
      justify-content: center;
      width: 90%;
      height: 25vh;
      padding: 5%;
    }

    form input {
      padding: 10px;
      width: 500px;
    }

    .cart-icon {
      position: relative;
      display: flex;
      align-items: center;
    }

    .cart-count {
      background-color: tomato;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 12px;
      position: absolute;
      top: -5px;
      right: -5px;
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background-color: #333;
      color: white;
    }

    .user-info {
      color: white;
      display: flex;
      align-items: center;
      margin-left: auto;
      padding-right: 20px;
    }

    .user-info img {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      margin-right: 10px;
    }
  </style>
</head>

<body>
  <nav class="navbar">
    <a href="ACCUEIL.php" class="hideonmobile">ACCUEIL</a>
    <a href="ABOUT.php" class="hideonmobile">A PROPOS</a>
    <a href="SERVICES.php" class="hideonmobile">SERVICES</a>
    <a href="CONTACT.php" class="hideonmobile">CONTACT</a>
    <a href="LEGAL.php" class="hideonmobile">LEGAL</a>
    <a href="cart.php" class="cart-icon" style="margin-right: auto; ">
      <img src="img/cart-icon.png" alt="Cart" width="25">
      <?php if (!empty($_SESSION['cart'])): ?>
        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?></span>
      <?php endif; ?>
    </a>
    <div class="user-info" style="color: white; ">
      <?php if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])): ?>
        <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="User Photo">
      <?php endif; ?>
      <?= htmlspecialchars($_SESSION['username']) ?>
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="ADMIN/DASHBOARD.php" style="color: white; margin-left: 10px;">(Admin)</a>
      <?php endif; ?>
      <a href="LOGOUT.php" style="color: white; margin-left: 10px;">Logout</a>
    </div>
    <a href="#" class="menu" onclick="showsidebar()">☰</a>
  </nav>

  <div class="sidebar">
    <a href="#" onclick="hidesidebar()">X</a>
    <a href="ACCUEIL.php">ACCUEIL</a>
    <a href="ABOUT.php">A PROPOS</a>
    <a href="SERVICES.php">SERVICES</a>
    <a href="CONTACT.php">CONTACT</a>
    <a href="LEGAL.php">LEGAL</a>
  </div>
  <script>
    function showsidebar() {
      const sidebar = document.querySelector(".sidebar");
      sidebar.style.display = "flex";
    }

    function hidesidebar() {
      const sidebar = document.querySelector(".sidebar");
      sidebar.style.display = "none";
    }
  </script>
  <div class="search-container">
    <form method="GET" action="SERVICES.php">
      <label for="search">
        <h2>Feel Free to search anything you Want !!</h2>
      </label><br>
      <input type="text" placeholder="Search.." name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    </form>
  </div>
  <div class="slider">
    <div class="slides">
      <img src="img/slide-img-257-1-1.png" alt="Image 1" />
      <img src="img/slide-img-254-1-1.png" alt="Image 2" />
      <img src="img/slide-img-240-1-1.png" alt="Image 3" />
      <img src="img/slide-img-258-1-1.png" alt="Image 4" />
      <img src="img/slide-img-252-1-1.png" alt="Image 5" />
    </div>
  </div>
  <div class="container3">
    <h1 class="section-header">Laptop Gamer:</h1>
    <div class="cards">
      <?php
      displayProducts($laptops);
      ?>
    </div>
  </div>
  <div class="container3">
    <h1 class="section-header">PC Gamer:</h1>
    <div class="cards">
      <?php
      if ($searchQuery) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE 
                                  (name LIKE ? OR short_description LIKE ? OR description LIKE ?) 
                                  AND category = 'PC Gamer'");
        $searchParam = "%$searchQuery%";
        $stmt->execute([$searchParam, $searchParam, $searchParam]);
        $pcs = $stmt->fetchAll();
      } else {
        $pcs = $pdo->query("SELECT * FROM products WHERE category = 'PC Gamer'")->fetchAll();
      }

      displayProducts($pcs);
      ?>
    </div>
  </div>
  <div class="container3">
    <h1 class="section-header">Accessories:</h1>
    <div class="cards">
      <?php

      if ($searchQuery) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE 
                                  (name LIKE ? OR short_description LIKE ? OR description LIKE ?) 
                                  AND category = 'Accessories'");
        $searchParam = "%$searchQuery%";
        $stmt->execute([$searchParam, $searchParam, $searchParam]);
        $accessories = $stmt->fetchAll();
      } else {
        $accessories = $pdo->query("SELECT * FROM products WHERE category = 'Accessories'")->fetchAll();
      }

      displayProducts($accessories);
      ?>
    </div>
    <?php
  
    $stmt = $pdo->query("SELECT DISTINCT category FROM products");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($categories as $category) {
      if (!in_array($category, ['Laptop Gamer', 'PC Gamer', 'Accessories'])) {
        echo '<div class="container3">';
        echo '<h1 class="section-header">' . htmlspecialchars($category) . ':</h1>';
        echo '<div class="cards">';

        if ($searchQuery) {
          $stmt = $pdo->prepare("SELECT * FROM products WHERE 
                                    (name LIKE ? OR short_description LIKE ? OR description LIKE ?) 
                                    AND category = ?");
          $searchParam = "%$searchQuery%";
          $stmt->execute([$searchParam, $searchParam, $searchParam, $category]);
          $products = $stmt->fetchAll();
        } else {
          $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
          $stmt->execute([$category]);
          $products = $stmt->fetchAll();
        }

        displayProducts($products);

        echo '</div>';
        echo '</div>';
      }
    }
    ?>
  </div>
  <p>FOR MAKING AN ORDER LIVE US A MESSAGE IN THE CONTACT AREA . THANKS!</p>
  <section class="footer">
    <hr />
    <div class="footer-container">
      <div class="footer-about">
        <h2>JOTA</h2>
        <p>
          Your go-to store for laptops, desktops, and accessories tailored for
          tech enthusiasts and everyday users.
        </p>
      </div>
      <div class="footer-links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="ACCUEIL.php">Home</a></li>
          <li><a href="ABOUT.php">About Us</a></li>
          <li><a href="SERVICES.php">Services</a></li>
          <li><a href="CONTACT.php">Contact</a></li>
          <li><a href="LEGAL.php">Legal</a></li>
        </ul>
      </div>
      <div class="footer-social">
        <h3>Follow Us</h3>
        <a href="#"><img src="img/facebook.png" alt="Facebook" /></a>
        <a href="#"><img src="img/twitter.png" alt="Twitter" /></a>
        <a href="#"><img src="img/instagram.png" alt="Instagram" /></a>
      </div>
      <div class="footer-contact">
        <h3>Contact Us</h3>
        <p>Email: support@jota.com</p>
        <p>Phone: +212 688 7*****</p>
        <p>Address: 123 HEYflah Street, Casablanca,</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> JOTA. All Rights Reserved.</p>
    </div>
  </section>
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('search') && urlParams.get('search').trim() !== '') {
        const target = document.querySelector('.container3');
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth'
          });
        }
      }
    });
  </script>
</body>

</html>