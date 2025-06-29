<?php
include "DB.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['description'] ?? '';

    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message, is_read, created_at) 
                                  VALUES (?, ?, ?, ?, 0, NOW())");
            $stmt->execute([$name, $email, $subject, $message]);

            $_SESSION['message_sent'] = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['error'] = "Failed to send message. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Please fill all required fields.";
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
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background-color: #333;
      color: white;
    }

    nav a {
      color: white;
      text-decoration: none;
      padding: 10px;
      transition: color 0.3s ease;
    }

    nav a:hover {
      color: #ff4e4e;
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

    .sidebar {
      display: none;
      flex-direction: column;
      position: fixed;
      top: 0;
      left: 0;
      width: 200px;
      height: 100%;
      background-color: #333;
      padding: 20px;
      z-index: 1000;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      margin-bottom: 10px;
    }

    .sidebar a:hover {
      color: #ff4e4e;
    }

    .bigbox {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      width: 100%;
      background-color: #f5f5f5;
      padding: 20px;
      box-sizing: border-box;
    }

    .support-box {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      height: auto;
      width: 100%;
      padding: 20px;
      margin: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 5px 10px 20px rgba(227, 156, 156, 0.8);
      background: white;
      animation: slideUp 1s ease-in-out;
    }

    .support-box h3 {
      font-weight: 500;
      font-size: 1.8rem;
      margin-bottom: 10px;
      color: #ff4e4e;
    }

    .support-box p {
      font-size: 1rem;
    }

    form {
      display: flex;
      flex-direction: column;
      width: 60%;
      margin: 0 auto;
      padding: 20px;
      border-radius: 10px;
      background-color: white;
      box-shadow: 5px 10px 20px rgba(0, 0, 0, 0.1);
      animation: fadeIn 1s ease-in-out;
    }

    label {
      font-weight: bold;
      margin: 10px 0 5px;
    }

    input,
    textarea {
      width: 90%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      padding: 10px 20px;
      background-color: #333;
      width: 50%;
      align-self: center;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
      background-color: #ff4e4e;
      transform: scale(1.05);
    }

    .footer {
      background-color: #1c1c1c;
      color: white;
      padding: 20px 0;
      text-align: center;
      margin-top: 50px;
      animation: fadeIn 2s ease-in-out;
    }

    .footer-container {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      padding: 20px;
    }

    .footer-container div {
      margin: 10px;
    }

    .footer-container h2,
    .footer-container h3 {
      color: #ff4e4e;
    }

    .footer-bottom {
      margin-top: 20px;
    }

    .alert {
      padding: 15px;
      margin: 20px auto;
      width: 60%;
      border-radius: 5px;
      text-align: center;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    @keyframes fadeIn {
      from {
        opacity: 0, 5;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes slideUp {
      from {
        transform: translateY(30px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @media (max-width: 768px) {
      .support-box {
        width: 90%;
      }

      form {
        width: 90%;
      }

      .footer-container {
        flex-direction: column;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      .support-box h3 {
        font-size: 1.5rem;
      }

      button {
        padding: 8px 16px;
      }
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
    <div class="user-info" style="color: white;">
      <?php if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])): ?>
        <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="User Photo" style="background-color: white ;">
      <?php endif; ?>
      <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="ADMIN/DASHBOARD.php" style="color: white; margin-left: 10px;">(Admin)</a>
      <?php endif; ?>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="LOGOUT.php" style="color: white; margin-left: 10px;">Logout</a>
      <?php endif; ?>
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
      <div class="support-box">
        <h3>Need Support? Need to contact us?</h3>
        <br />
        <p>
          Our support team is available 24/7 to help you with any issues you
          might have.
        </p>
      </div>
    </div>
  </div>

  <?php if (isset($_SESSION['message_sent'])): ?>
    <div class="alert alert-success">
      Your message has been sent successfully! We'll get back to you soon.
    </div>
    <?php unset($_SESSION['message_sent']); ?>
  <?php elseif (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
      <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form method="POST" action="">
    <label for="name">Name:</label>
    <input
      type="text"
      id="name"
      name="name"
      required
      placeholder="NAME" /><br />

    <label for="email">Email:</label>
    <input
      type="email"
      id="email"
      name="email"
      required
      placeholder="ABCDEFG@GMAIL.COM" /><br />

    <label for="subject">Subject:</label>
    <input
      type="text"
      id="subject"
      name="subject"
      required
      placeholder="Subject" /><br />

    <label for="description">Brief description of how JOTA support can help you:</label>
    <textarea
      id="description"
      name="description"
      required
      placeholder="Thanks for choosing JOTA !!"></textarea>

    <button type="submit">SUBMIT</button>
  </form>
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
      <p>&copy; 2024 JOTA. All Rights Reserved.</p>
    </div>
  </section>
</body>

</html>