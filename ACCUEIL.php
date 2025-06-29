<?php
include 'DB.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: LOGIN.php');
  exit;
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

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background-color: #333;
      color: white;
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
    <a href="cart.php" class="cart-icon">
      <img src="img/cart-icon.png" alt="Cart" width="25">
      <?php if (!empty($_SESSION['cart'])): ?>
        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?></span>
      <?php endif; ?>
    </a>
    <div class="user-info" style="color: white;">
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
  <article>
    <section class="main">
      <div class="container">
        <div class="content">
          <h1>WELCOME TO <span id="jota">JOTA</span></h1>
          <h2>Your One-Stop Shop for PCs</h2>
          <p>
            At JOTA, we provide top-notch laptops, desktops, and accessories for
            gamers, professionals, and casual users. Experience unmatched quality
            and performance with our handpicked collections.
          </p>
          <a href="SERVICES.php" class="cta-button">Explore Our Products</a>
        </div>
        <div class="main-image">
          <img src="img/jeu-informatique.png" alt="Featured PC" />
        </div>
      </div>
    </section>

    <section class="second">
      <div class="container2">
        <div class="content2">
          <h1>WHAT IS <span id="jota">JOTA</span> OFFERING,</h1>
          <p>Well to you were offering laptops ,desktop ,accessoires pc</p>
        </div>
        <div class="image2">
          <img src="img/gamingpc.png" alt="Image 1" />
          <img src="img/img22.png" alt="Image 2" />
          <img src="img/img3.png" alt="Image 3" />
        </div>
      </div>
    </section>
    <section class="tree">
      <div class="container3">
        <h1 class="section-header">SOME OF OUR BEST SELLING :</h1>
        <div class="cards">
          <div class="card">
            <img src="img/pcg1.jpg" alt="Landscape 1" />
            <h2>PC Gamer</h2>
            <p>Ryzen 5 5600 /16GB/512GB SSD/RTX3050 8GB</p>
          </div>
          <div class="card">
            <img src="img/pcg2.jpg" alt="Landscape 2" />
            <h2>ASUS TUF GAMING A15 FA506NFR-HN008</h2>
            <p>AMD R7-7435HS/16GB DDR5/512GB SSD/RTX2050 4GB/15.6'' 144Hz</p>
          </div>
          <div class="card">
            <img src="img/pcg3.jpg" alt="Landscape 3" />
            <h2>Logitech Pack</h2>
            <p>BUNDLE Logitech G213 + G335 + G102</p>
          </div>
        </div>
      </div>
    </section>

    <br>
    <section class="footer">
      <div class="footer-container">
        <div class="footer-about">
          <h2 id="jota">JOTA</h2>
          <p>
            Your go-to store for laptops, desktops, and accessories tailored
            for tech enthusiasts and everyday users.
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
  </article>
</body>

</html>