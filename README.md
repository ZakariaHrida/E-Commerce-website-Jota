Jota – Dynamic E-Commerce Platform for PC Sales
Jota is a modern, dynamic e-commerce website built to provide a smooth, fast, and intuitive shopping experience for users looking to buy PCs, gaming laptops, and computer components.
The platform includes an admin dashboard, product management, dynamic filtering, secure authentication, and a clean, responsive UI.

🚀 Features
🛒 User Side

Browse PCs and components with dynamic loading

Responsive and modern UI

Product details page with specifications

Search and category-based filtering

Add to cart (local storage or DB depending on version)

Multi-language support (EN/FR) (optional)

🛠️ Admin Dashboard

Add new products

Edit and update product details

Delete products

Real-time data loading (AJAX)

JSON-based or MySQL-based data storage (depending on configuration)

🎨 Design

Clean, modern, minimal interface

Fully responsive

Smooth animations

Black/white theme option

Dark/Light mode (optional)

🧱 Tech Stack
Frontend

HTML, CSS, JS

AJAX / Fetch API

TailwindCSS (optional)

React (if using SPA version)

Backend

PHP

getData.php & setData.php for CRUD operations

JSON file storage or MySQL database

Database (optional setup)

MySQL with products table

Dynamic create/update/delete entries

📂 Project Structure
/jota
│
├── /admin
│   ├── index.php
│   ├── addProduct.php
│   ├── editProduct.php
│   ├── deleteProduct.php
│   ├── getData.php
│   ├── setData.php
│   └── /assets
│
├── /public
│   ├── index.php
│   ├── product.php
│   ├── styles.css
│   └── main.js
│
├── /data
│   └── products.json
│
├── README.md
└── config.php

⚙️ Installation
1. Clone the repository
git clone https://github.com/yourusername/jota.git
cd jota

2. Configure the backend

If using JSON data storage (default):

No setup needed

Make sure /data/products.json is writable

If using MySQL:

Update config.php with your database credentials

Import database.sql (if included)

3. Run project using a local server

Use XAMPP, WAMP, Laragon or built-in PHP server:

php -S localhost:8000


Then open:

http://localhost:8000/public

🧪 Admin Login

Default credentials:

Username: admin
Password: admin123


(You can change these in database or config file.)

📌 Roadmap / Upcoming Features

User authentication system

Real payment gateway integration

Review & rating system

User dashboard

Order tracking

API for mobile app version

🤝 Contributing

Feel free to open issues, request features, or submit pull requests.
All contributions are welcome!

📜 License

This project is licensed under the MIT License – free to modify and use.
