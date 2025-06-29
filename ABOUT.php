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

    .founder {
      padding: 20px;
      height: 1fr;
      height: 50vh;
    }

    .founder #color {
      color: red;
    }

    .founder img {
      width: 200px;
      padding: 0 30px;
      float: left;
    }

    .founder p {
      padding-top: 30px;
    }

    .content {
      padding: 30px;
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

  <div class="content">
    <span id="jota">JOTA </span>is a boutique store committed to providing
    high-quality technology solutions for a diverse audience, ranging from
    avid tech enthusiasts to casual users. <br />
    Specializing in a carefully curated selection of laptops, desktops, and
    accessories, JOTA strives to meet the unique needs of each customer.
    <br />
    Whether you're a professional in need of a powerful workstation, a student
    seeking an affordable and reliable laptop, or a gamer looking to enhance
    your setup with cutting-edge gear, JOTA has something tailored just for
    you. <br />
    <big>Our</big> mission is to combine quality, affordability, and customer
    satisfaction, ensuring that every purchase you make is a step towards
    achieving your goals. <br />With a passion for innovation and a dedication
    to excellence, JOTA is not just a store—it's your trusted partner in
    technology.
  </div>

  <div class="founder">
    <h1>The founder of <span id="color">JOTA</span> :</h1>
    <div>
      <img src="img/founder.png" alt="Founder" />
      <p>
        Hrida Zakaria is the founder and CEO of JOTA. With a background in computer
        science and a passion for technology, Hrida has always been fascinated
        by the endless possibilities that technology offers. <br />
        After years of working in the tech industry, Hrida decided to combine
        their expertise and experience to create JOTA—a store that reflects their
        commitment to quality, innovation, and customer satisfaction. <br />
        Hrida's vision for JOTA is to provide customers with a seamless
        shopping experience, offering a range of products that cater to their
        unique needs. <br />
        Whether you're a seasoned professional or a casual user, Hrida believes
        that everyone deserves access to high-quality technology that enhances
        their daily life. <br />
        With a focus on customer service and product excellence, Hrida is
        dedicated to making JOTA your go-to destination for all your tech
        needs.
      </p>
    </div>
  </div>

  <section class="footer">
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
</body>

</html>