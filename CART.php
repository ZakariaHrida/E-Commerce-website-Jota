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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/icon.png">
    <title>JOTA - Shopping Cart</title>
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

        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .cart-item img {
            width: 100px;
            margin-right: 20px;
        }

        .cart-item-details {
            flex-grow: 1;
        }

        .cart-item-price {
            font-weight: bold;
            color: tomato;
        }

        .cart-total {
            text-align: right;
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .checkout-btn {
            background-color: tomato;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 20px;
            float: right;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .quantity-control button {
            background: #f0f0f0;
            border: 1px solid #ddd;
            padding: 5px 10px;
            cursor: pointer;
        }

        .quantity-control input {
            width: 50px;
            text-align: center;
            margin: 0 5px;
        }

        .remove-item {
            color: red;
            cursor: pointer;
            margin-left: 20px;
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
            <img src="img/cart-icon.png" alt="Cart" width="30">
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
                <a href="DASHBOARD.php" style="color: white; margin-left: 10px;">(Admin)</a>
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
        <a href="cart.php" class="cart-icon" style="margin-left: auto; padding-right: 20px;">
            <img src="img/cart-icon.png" alt="Cart" width="30">
            <?php if (!empty($_SESSION['cart'])): ?>
                <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?></span>
            <?php endif; ?>
        </a>
        <div class="user-info" style="color: white; margin-top: 30px;">
            <?php if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])): ?>
                <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="User Photo">
            <?php endif; ?>
            <?= htmlspecialchars($_SESSION['username']) ?>
            <a href="LOGOUT.php" style="color:rgb(255, 255, 255); margin-left: 10px;">Logout</a>
        </div>
    </div>

    <div class="cart-container">
        <h1>Your Shopping Cart</h1>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
            <a href="SERVICES.php" class="checkout-btn">Continue Shopping</a>
        <?php else: ?>
            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $item):
                $subtotal = (isset($item['price']) && isset($item['quantity'])) ? $item['price'] * $item['quantity'] : 0;
                $total += $subtotal;
            ?>
                <div class="cart-item">
                    <img src="img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="cart-item-details">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <div class="cart-item-price"><?= number_format($item['price'], 2, ',', ' ') ?> MAD</div>
                        <div class="quantity-control">
                            <form method="POST" action="update_cart.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" name="action" value="decrease">-</button>
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="10" readonly>
                                <button type="submit" name="action" value="increase">+</button>
                            </form>
                            <form method="POST" action="update_cart.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" name="action" value="remove" class="remove-item">Remove</button>
                            </form>
                        </div>
                        <div>Subtotal: <?= number_format($subtotal, 2, ',', ' ') ?> MAD</div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="cart-total">
                Total: <?= number_format($total, 2, ',', ' ') ?> MAD
            </div>
            <button class="checkout-btn">Proceed to Checkout</button>
        <?php endif; ?>
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