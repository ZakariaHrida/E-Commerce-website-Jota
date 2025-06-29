<?php
include 'DB.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: LOGIN.php');
  exit;
}


$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
  die("Product not found");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  $quantity = intval($_POST['quantity']);
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
  } else {
    $_SESSION['cart'][$product_id] = [
      'id' => $product_id,
      'name' => $product['name'],
      'price' => $product['price'],
      'quantity' => $quantity,
      'image' => $product['image']
    ];
  }

  header('Location: CART.php');
  exit;
}


$related_products = $pdo->query("SELECT * FROM products WHERE category = '{$product['category']}' AND id != $product_id LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="img/icon.png" />
  <link rel="stylesheet" href="style.css" />
  <title>JOTA - <?= htmlspecialchars($product['name']) ?></title>
  <style>
    body {
      background-color: white;
    }

    .flex-box {
      display: flex;
      width: 1000px;
      height: 70vh;
      margin: 20px auto;
    }

    .left {
      width: 40%;
    }

    .big-img {
      border: 1px solid rgb(219, 219, 219);
      box-shadow: #b9b9b9 0px 2px 10px;
      width: 350px;
    }

    .big-img img {
      width: inherit;
    }

    .images {
      display: flex;
      justify-content: space-between;
      width: 70%;
      margin-top: 15px;
    }

    .small-img {
      width: 60px;
    }

    .small-img img {
      width: inherit;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .small-img:hover img {
      transform: scale(1.2);
    }

    a {
      text-decoration: none;
      color: black;
    }

    .right {
      width: 60%;
    }

    .url {
      float: right;
      font-size: 12px;
      color: grey;
    }

    .prix {
      font-size: 30px;
      margin-left: 20px;
      color: rgb(0, 143, 152);
    }

    .smp {
      margin-left: 10px;
      font-size: 15px;
      color: grey;
    }

    .cart-form {
      text-align: center;
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0.1, 0.1);
    }

    #quantity {
      width: 50px;
      text-align: center;
      font-size: 16px;
      margin: 0 5px;
    }

    .add-to-cart-btn {
      background-color: tomato;
      color: white;
      border: none;
      padding: 10px 15px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 4px;
    }

    .add-to-cart-btn:hover {
      background-color: rgb(222, 62, 34);
    }

    .product-details {
      max-width: 1200px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      border-bottom: 2px solid #000000;
      padding-bottom: 5px;
      color: #333;
    }

    .description p {
      font-size: 16px;
      color: #555;
    }

    .fiche-technique table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }

    .fiche-technique th,
    .fiche-technique td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    .fiche-technique th {
      background-color: #f2f2f2;
      color: #333;
    }

    .slider {
      margin-top: 20px;
    }

    .card-container {
      display: flex;
      overflow-x: auto;
      gap: 15px;
      padding: 10px 0;
      height: auto;
    }

    .card {
      flex: 0 0 250px;
      background: rgb(219, 219, 219);
      color: rgb(0, 0, 0);
      border-radius: 5px;
      padding: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .card:hover {
      transform: scale(0.95);
      transition: transform 0.5s;
    }

    .card-container::-webkit-scrollbar {
      height: 8px;
    }

    .card-container::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 4px;
    }

    .card-container::-webkit-scrollbar-thumb:hover {
      background: #aaa;
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

    @media (max-width: 400px) {
      .product-details {
        padding: 10px;
      }

      .description p {
        font-size: 14px;
      }

      .fiche-technique th,
      .fiche-technique td {
        font-size: 12px;
      }

      .card {
        flex: 0 0 150px;
      }
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
    <a href="cart.php" class="cart-icon" style="margin-right: auto; ">
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
        <a href="dashboard.php" style="color: white; margin-left: 10px;">(Admin)</a>
      <?php endif; ?>
      <a href="logout.php" style="color: white; margin-left: 10px;">Logout</a>
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
    <a href="cart.php" class="cart-icon" style="margin-left: auto; padding-right: 20px;">
      <img src="img/cart-icon.png" alt="Cart" width="30">
      <?php if (!empty($_SESSION['cart'])): ?>
        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?></span>
      <?php endif; ?>
    </a>
    <div class="user-info" style="color: #1C1C1C; margin-top: 20px;">
      <?php if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])): ?>
        <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="User Photo">
      <?php endif; ?>
      <?= htmlspecialchars($_SESSION['username']) ?>
      <a href="logout.php" style="color: #1C1C1C; margin-left: 10px;">Logout</a>
    </div>
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
  <div class="flex-box">
    <div class="left">
      <div class="big-img">
        <img src="img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
      </div>
      <div class="images">
        <div class="small-img">
          <img src="img/<?= htmlspecialchars($product['image']) ?>" alt onclick="showImg(this.src)" />
        </div>
        <?php if (!empty($product['image2'])): ?>
          <div class="small-img">
            <img src="img/<?= htmlspecialchars($product['image2']) ?>" alt onclick="showImg(this.src)" />
          </div>
        <?php endif; ?>
        <?php if (!empty($product['image3'])): ?>
          <div class="small-img">
            <img src="img/<?= htmlspecialchars($product['image3']) ?>" alt onclick="showImg(this.src)" />
          </div>
        <?php endif; ?>
        <?php if (!empty($product['image4'])): ?>
          <div class="small-img">
            <img src="img/<?= htmlspecialchars($product['image4']) ?>" alt onclick="showImg(this.src)" />
          </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="right">
      <div class="url">
        <a href="ACCUEIL.php">ACCEUIL</a> > <a href="SERVICES.php">SERVICES</a> > <?= htmlspecialchars($product['category']) ?> > <?= htmlspecialchars($product['name']) ?>
      </div>
      <br />
      <h1><?= htmlspecialchars($product['name']) ?></h1>
      <p>Référence : <?= htmlspecialchars($product['reference']) ?></p>
      <div class="prix"><?= number_format($product['price'], 2, ',', ' ') ?> MAD</div>
      <hr />
      <p class="smp">
        <?= htmlspecialchars($product['short_description']) ?>
      </p>
      <hr />
      <div class="cart-form">
        <form method="POST">
          <label for="quantity">Quantity:</label>
          <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" title="Enter quantity" placeholder="1" />
          <button type="submit" name="add_to_cart" class="add-to-cart-btn">
            Ajouter au panier
          </button>
        </form>
      </div>
    </div>
  </div>
  <div class="product-details">
    <div class="description">
      <h2>Description</h2>
      <p>
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </p>
    </div>

    <div class="fiche-technique">
      <h2>Fiche Technique</h2>
      <table>
        <tr>
          <th>Type de Processeur</th>
          <td><?= htmlspecialchars($product['processor']) ?></td>
        </tr>
        <tr>
          <th>Capacité RAM</th>
          <td><?= htmlspecialchars($product['ram']) ?></td>
        </tr>
        <tr>
          <th>Chipset graphique</th>
          <td><?= htmlspecialchars($product['graphics']) ?></td>
        </tr>
        <tr>
          <th>SSD</th>
          <td><?= htmlspecialchars($product['ssd']) ?></td>
        </tr>
        <tr>
          <th>Écran</th>
          <td><?= htmlspecialchars($product['screen']) ?></td>
        </tr>
        <tr>
          <th>Clavier</th>
          <td><?= htmlspecialchars($product['keyboard']) ?></td>
        </tr>
        <tr>
          <th>Système d'exploitation</th>
          <td><?= htmlspecialchars($product['os']) ?></td>
        </tr>
        <tr>
          <th>Garantie</th>
          <td><?= htmlspecialchars($product['warranty']) ?></td>
        </tr>
        <tr>
          <th>Marque</th>
          <td><?= htmlspecialchars($product['brand']) ?></td>
        </tr>
      </table>
    </div>

    <?php if (!empty($related_products)): ?>
      <div class="slider">
        <h2> Autres Produits Dans La Même Catégorie</h2>
        <div class="card-container">
          <?php foreach ($related_products as $related): ?>
            <div class="card">
              <a href="BUY.php?id=<?= $related['id'] ?>">
                <img src="img/<?= htmlspecialchars($related['image']) ?>" alt="<?= htmlspecialchars($related['name']) ?>" />
                <h3><?= htmlspecialchars($related['name']) ?></h3>
                <p><?= htmlspecialchars($related['short_description']) ?></p>
                <p>PRICE : <?= number_format($related['price'], 2, ',', ' ') ?>dh</p>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <script>
    let bigImg = document.querySelector(".big-img img");

    function showImg(pic) {
      bigImg.src = pic;
    }
  </script>
  <section class="footer">
    <hr />
    <div class="footer-container">
      <div class="footer-about">
        <h3>JOTA</h3>
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