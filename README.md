🚲🛍️ Point A to B Delivery System
📌 Overview

This project is a Point A to B Delivery Web System designed to make logistics simple and efficient.
Users can request deliveries from one location (Point A) to another (Point B), and drivers can accept, update, and complete those requests.

The system supports multiple roles:

Super Admin – full control of admins, drivers, and customers

Admin – manage deliveries, assign drivers, generate reports

Driver – set up profile, view assigned deliveries, update status, share live location

Sender/User – create delivery requests, track progress in real-time

The main goal is to provide a pickup-and-drop delivery platform with real-time tracking, secure authentication, and role-based management.

🚀 Tech Stack

Frontend: PHP, Bootstrap, JavaScript (Vanilla + JSON handling)

Backend: PHP

Database: MySQL

Maps & Routing: Integrated with geolocation & map APIs

Real-Time Features: Live location updates via JavaScript/JSON

✨ Features

✔️ Create, edit, and track delivery requests
✔️ Real-time driver location updates
✔️ Driver profile setup (vehicle, license, experience, photos)
✔️ Role-based dashboards (Admin, Driver, Sender)
✔️ Secure login & registration for users and admins
✔️ Delivery management with status updates

📊 Future Improvements

🔹 Push notifications for delivery updates
🔹 Improved analytics with data visualization (charts, trends)
🔹 React front-end migration for scalability
🔹 Stronger API integrations for maps & routing

🏗️ Project Structure
DELIVERY/
│── .vscode/                 # VS Code settings
│── Assets/
│   └── images/              # Project images
│
│── Dashboard/               # Admin dashboard features
│   ├── admin_login.php
│   ├── admin_register.php
│   ├── dashboard.php
│   └── test.php
│
│── Database/                # Database connection and schema
│   ├── db.php
│   └── db.sql
│
│── DeliveryFeatures/        # Delivery request features
│   ├── create_delivery.php
│   ├── delete_delivery.php
│   ├── edit_delivery.php
│   └── view_deliveries.php
│
│── DriverFeatures/          # Driver-related features
│   ├── accept_delivery.php
│   ├── driver_dashboard.php
│   ├── update_delivery_status.php
│   └── view_assigned_deliveries.php
│
│── includes/                # Common includes
│   ├── footer_tags.php
│   └── head_tags.php
│
│── uploads/                 # File uploads (driver documents/photos)
│   └── drivers/
│       ├── 1758364557_license.jpg
│       └── 1758364557_profile.jpeg
│
│── create_super_admin.php   # Super admin setup
│── driver_setup.php         # Driver profile setup
│── geocode_proxy.php        # Proxy for geocoding
│── home.php                 # Home/landing page
│── login.php                # User login
│── logout.php               # User logout
│── register.php             # User registration
│── style.css                # Global stylesheet
│── update_location.php      # Driver location updater
│── uploads.php              # Upload handler
│── README.md                # Project documentation

📖 About Me

I am an aspiring Software Engineer with practical experience in web development, databases, and real-time applications.

🎓 Level 5 Diploma in Computing – NCC Education
💻 Skilled in PHP, MySQL, JavaScript, Bootstrap
🌱 Currently learning React
🔍 Interested in solving problems in logistics and delivery systems

🤝 Let’s Connect

💼 LinkedIn
 (add your profile link)
📧 Email: your-email@example.com

🖥️ Portfolio: (if available)

Would you like me to also add a Setup Guide (installation, database import, and how to run on XAMPP) to this README? That would make it easy for your teacher to test the project.

You said:
yes
ChatGPT said:

Perfect ✅ I’ll add a Setup Guide section to your README so your teacher can easily install and run your project in XAMPP. Here’s the improved README:

🚚🌍 Point A to B Delivery System
📌 Overview

This project is a Point A to B Delivery Web System designed to make logistics simple and efficient.
Users can request deliveries from one location (Point A) to another (Point B), and drivers can accept, update, and complete those requests.

The system supports multiple roles:

Super Admin – full control of admins, drivers, and customers

Admin – manage deliveries, assign drivers, generate reports

Driver – set up profile, view assigned deliveries, update status, share live location

Sender/User – create delivery requests, track progress in real-time

The main goal is to provide a pickup-and-drop delivery platform with real-time tracking, secure authentication, and role-based management.

🚀 Tech Stack

Frontend: PHP, Bootstrap, JavaScript (Vanilla + JSON handling)

Backend: PHP

Database: MySQL

Maps & Routing: Integrated with geolocation & map APIs

Real-Time Features: Live location updates via JavaScript/JSON

✨ Features

✔️ Create, edit, and track delivery requests
✔️ Real-time driver location updates
✔️ Driver profile setup (vehicle, license, experience, photos)
✔️ Role-based dashboards (Admin, Driver, Sender)
✔️ Secure login & registration for users and admins
✔️ Delivery management with status updates

📊 Future Improvements

🔹 Push notifications for delivery updates
🔹 Improved analytics with data visualization (charts, trends)
🔹 React front-end migration for scalability
🔹 Stronger API integrations for maps & routing

🏗️ Project Structure
DELIVERY/
│── .vscode/                 # VS Code settings
│── Assets/
│   └── images/              # Project images
│
│── Dashboard/               # Admin dashboard features
│   ├── admin_login.php
│   ├── admin_register.php
│   ├── dashboard.php
│   └── test.php
│
│── Database/                # Database connection and schema
│   ├── db.php
│   └── db.sql
│
│── DeliveryFeatures/        # Delivery request features
│   ├── create_delivery.php
│   ├── delete_delivery.php
│   ├── edit_delivery.php
│   └── view_deliveries.php
│
│── DriverFeatures/          # Driver-related features
│   ├── accept_delivery.php
│   ├── driver_dashboard.php
│   ├── update_delivery_status.php
│   └── view_assigned_deliveries.php
│
│── includes/                # Common includes
│   ├── footer_tags.php
│   └── head_tags.php
│
│── uploads/                 # File uploads (driver documents/photos)
│   └── drivers/
│       ├── 1758364557_license.jpg
│       └── 1758364557_profile.jpeg
│
│── create_super_admin.php   # Super admin setup
│── driver_setup.php         # Driver profile setup
│── geocode_proxy.php        # Proxy for geocoding
│── home.php                 # Home/landing page
│── login.php                # User login
│── logout.php               # User logout
│── register.php             # User registration
│── style.css                # Global stylesheet
│── update_location.php      # Driver location updater
│── uploads.php              # Upload handler
│── README.md                # Project documentation

⚙️ Setup Guide

Follow these steps to run the project locally using XAMPP:

1️⃣ Install Requirements

XAMPP
 (with Apache & MySQL)

Web browser (Chrome/Firefox recommended)

VS Code or any code editor

2️⃣ Clone or Download Project

Place the project folder inside your XAMPP htdocs directory:

/Applications/XAMPP/htdocs/DELIVERY   (Mac)
C:/xampp/htdocs/DELIVERY              (Windows)

3️⃣ Database Setup

Start Apache and MySQL in XAMPP.

Open phpMyAdmin at http://localhost/phpmyadmin
.

Create a new database (e.g., delivery_db).

Import the provided SQL file:

Go to Import tab

Select Database/db.sql

Click Go

4️⃣ Configure Database

Edit the file Database/db.php and update database credentials if needed:

<?php
$host = "localhost";
$user = "root";     // default XAMPP user
$pass = "";         // default XAMPP password is empty
$db   = "delivery_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

5️⃣ Run the Project

Open your browser and go to:

http://localhost/DELIVERY/home.php


Register as a User (Sender) or Driver

Admin can log in via Dashboard/admin_login.php

Super Admin can be created via create_super_admin.php

📖 About Me

I am an aspiring Software Engineer with practical experience in web development, databases, and real-time applications.

🎓 Level 5 Diploma in Computing – NCC Education
💻 Skilled in PHP, MySQL, JavaScript, Bootstrap
🌱 Currently learning React
🔍 Interested in solving problems with tech

🤝 Let’s Connect

📧 Email: stevieaung90@gmail.com
📞 Phone: +959777395589
