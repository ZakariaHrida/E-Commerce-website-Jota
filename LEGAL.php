<?php
include "DB.php";
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
    .bigbox {
      width: 100%;
      height: auto;
      display: flex;
      justify-content: center;
      align-content: center;
    }

    .container {
      height: auto;
      width: 100%;
      display: flex;
      justify-content: center;
      align-content: center;
    }

    .legal-box {
      width: 50%;
      padding: 20px;
      margin-bottom: 100px;
      border-radius: 5px;
      text-align: center;
      box-shadow: 5px 10px 20px rgb(0, 0, 0);
    }

    p {
      margin: 10px 0;
    }

    li {
      margin: 10px 0;
    }

    .footer {
      background-color: #1C1C1C;
      color: white;
      text-align: center;
    }

    a {
      color: rgb(223, 217, 217);
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
    <a href="cart.php" class="cart-icon" style="margin-right: auto;">
      <img src="img/cart-icon.png" alt="Cart" width="25">
      <?php if (!empty($_SESSION['cart'])): ?>
        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?></span>
      <?php endif; ?>
    </a>
    <div class="user-info" style="color: white;  ">
      <?php if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])): ?>
        <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="User Photo" style="background-color: white ;">
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
  <div class="bigbox">
    <div class="container">
      <div class="legal-box">
        <h2>Mentions Légales</h2>
        <p>Dernière mise à jour: Décembre 2024</p>

        <h3>Propriétaire du site</h3>
        <p>Le site web <strong>JOTA</strong> est édité par :</p>
        <p><strong>ZAKARIA</strong></p>
        <p>Adresse : 123 HEYflah Street, Casablanca, Maroc</p>
        <p>Email : <a href="mailto:support@jota.com">support@jota.com</a></p>
        <p>Téléphone : +212 688 7*****</p>

        <h3>Hébergement</h3>
        <p>Le site est hébergé par :</p>
        <p><strong>ZAKARIA</strong></p>
        <p>Adresse : CASABLANCA</p>
        <p>Email : zakariahrida05@gmail.com</p>

        <h3>Conditions d'utilisation</h3>
        <p>
          En accédant au site JOTA, vous acceptez les conditions d'utilisation
          suivantes :
        </p>
        <ul>
          <li>
            Le contenu du site est protégé par des droits d'auteur. Toute
            reproduction est interdite sans autorisation préalable.
          </li>
          <li>
            Les informations fournies sur le site sont à titre informatif
            uniquement.
          </li>
          <li>
            Nous nous réservons le droit de modifier le contenu du site à tout
            moment sans préavis.
          </li>
        </ul>

        <h3>Propriété intellectuelle</h3>
        <p>
          Le contenu du site, y compris les textes, images, logos et autres
          éléments graphiques, est la propriété exclusive de JOTA ou de ses
          partenaires et est protégé par les lois sur la propriété
          intellectuelle.
        </p>

        <h3>Protection des données personnelles</h3>
        <p>
          Nous nous engageons à protéger vos données personnelles conformément à
          la législation en vigueur sur la protection des données personnelles.
          Pour plus d'informations, veuillez consulter notre
          <a href="#">politique de confidentialité</a>.
        </p>

        <h3>Limitation de responsabilité</h3>
        <p>
          Le site peut contenir des liens vers d'autres sites web. JOTA ne peut
          être tenu responsable du contenu de ces sites externes et de
          l'utilisation que vous en faites.
        </p>

        <h3>Cookies</h3>
        <p>
          Ce site utilise des cookies pour améliorer votre expérience de
          navigation. En poursuivant votre navigation, vous acceptez
          l'utilisation de ces cookies.
        </p>
      </div>
    </div>
  </div>
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
      <p>&copy; 2024 JOTA. All Rights Reserved.</p>
    </div>
  </section>
</body>

</html>