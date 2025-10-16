# 🛵 A To B Delivery Service Website
### PHP–MySQL | Bootstrap 5 | Leaflet.js
**Developer:** Sai Htet Aung Hlaing  
**Project Type:** Level 5 Diploma in Computing — Computing Project  
**Supervisor:** Dr. Aye Aye, Strategy First International College, Mandalay  

---

## 📘 Project Overview
The **A To B Delivery Service Website** is a PHP-based web application that connects **senders** and **drivers** for local point-to-point deliveries.  
It automates delivery requests, tracking, and management through a responsive web platform using **Leaflet.js maps** for real-time delivery visualization.  

The system enables:  
- Senders to request and track deliveries  
- Drivers to accept, update, and complete jobs  
- Admins to oversee operations and generate reports  

This project integrates **database-driven workflows**, **secure user management**, and **interactive mapping**, reflecting the principles of full-stack web development for logistics optimization.

---

## 🎯 Project Aim
To develop a **scalable, secure, and interactive web application** that manages local deliveries efficiently and enhances transparency between senders, drivers, and administrators.

---

## 🧩 Key Objectives
- Build a **modular PHP-MySQL** web system with responsive design.  
- Integrate **role-based modules** for senders, drivers, and admins.  
- Implement **map-based delivery tracking** using Leaflet.js and OpenStreetMap.  
- Enable real-time delivery status updates.  
- Provide **data analytics** and performance reporting tools for administrators.

---

## ⚙️ System Features

### 👤 Sender Module
- Register and log in securely  
- Create and manage delivery requests  
- Track current delivery on a **Leaflet-based live map**  
- View completed deliveries and receipts  
- Submit ratings and feedback  

### 🛻 Driver Module
- Register with vehicle and license details  
- View and accept available deliveries  
- Update delivery progress (picked up, in transit, delivered)  
- Live location updates on the map  
- View income and performance reports  

### 🧑‍💼 Admin Module
- Manage all senders, drivers, and delivery records  
- Assign deliveries and monitor statuses  
- View system reports and analytics (charts)  
- Manage pricing, regions, and settings  
- Access super admin features for user management  

### 🔐 Security & Authentication
- Secure user sessions with role-based access  
- Password encryption using `password_hash()`  
- Form validation and CSRF protection  
- Input sanitization and restricted file uploads  

---

## 🧱 System Architecture

| Layer | Technologies | Description |
|--------|---------------|-------------|
| **Frontend** | HTML5, CSS3, JavaScript, Bootstrap 5 | Responsive UI framework |
| **Map Integration** | Leaflet.js + OpenStreetMap | Interactive, API-free map visualization |
| **Backend** | PHP 8+ (Procedural + Modular MVC) | Business logic and routing |
| **Database** | MySQL 8 (InnoDB Engine) | Relational data storage |
| **Server** | Apache (XAMPP / LAMP) | Localhost runtime environment |

---

## 🗄️ Database Schema (Core Tables)

| Table | Description |
|--------|-------------|
| `users` | Sender and driver user accounts |
| `driver_profiles` | Driver documents and vehicle data |
| `delivery_requests` | Main delivery order details |
| `delivery_tracking` | Live tracking and delivery status |
| `admins` | Admin and super admin records |
| `payments` | Financial and transaction data |
| `feedback` | Ratings and user feedback |

---

## 📁 Folder Structure (Current Version)
```
Delivery/
├── Admin/
│   ├── dashboard.php
│   ├── manage_users.php
│   ├── manage_deliveries.php
│   ├── reports.php
│   ├── settings.php
│   ├── includes/
│   └── middleware/
│
├── Driver/
│   ├── driver_dashboard.php
│   ├── available_deliveries.php
│   ├── my_deliveries.php
│   ├── update_tracking.php
│   ├── earnings.php
│
├── DeliveryFeatures/
│   ├── create_delivery.php
│   ├── view_deliveries.php
│   ├── track_delivery.php
│
├── Database/
│   ├── db.php
│   ├── db.sql
│
├── includes/
│   ├── navbar.php
│   ├── footer.php
│   ├── head_tags.php
│
├── api/
│   ├── get_driver_location.php
│
├── uploads/
│   └── drivers/
│
├── login.php
├── register.php
├── home.php
├── about.php
├── services.php
├── logout.php
└── README.md
```

---

## 🧠 Software Development Methodology
The project follows the **Agile Scrum** approach — iterative, feedback-driven, and sprint-based development.

| Sprint | Focus | Deliverables |
|--------|--------|--------------|
| 1 | Database design & authentication | `users`, `driver_profiles`, `admins` |
| 2 | Core sender & driver workflows | Delivery creation, acceptance, updates |
| 3 | Admin dashboard | Reports, analytics, user management |
| 4 | UI/UX improvement & Leaflet map | Map-based tracking, final testing |

---

## 🗺️ Leaflet Map Integration

**Libraries Used:**
- [Leaflet.js](https://leafletjs.com/)
- OpenStreetMap tiles (free, no API key required)
- Optional: [Leaflet Routing Machine](https://www.liedman.net/leaflet-routing-machine/) for route visualization  

**Example Setup:**
```html
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div id="map" style="height: 400px;"></div>

<script>
  var map = L.map('map').setView([21.9588, 96.0891], 13); // Default Mandalay
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);
</script>
```

---

## 📊 Reports & Analytics
- Delivery Performance Report  
- Driver Earnings Summary  
- User Registration Trends  
- Delivery Volume per Month  
- Feedback Analysis  

---

## 🌍 Future Extensions
- Progressive Web App (PWA) for mobile  
- RESTful API for Android app integration  
- Wallet system and QR-based payments  
- Predictive analytics for delivery timing  
- Multi-language interface (English/Myanmar)  

---

## 🚀 Installation Guide (Localhost Setup)

1. **Extract** the folder to:  
   ```
   C:\xampp\htdocs\A2B-Delivery
   ```

2. **Create database:**  
   - Open phpMyAdmin  
   - Create new DB: `a2b_delivery`  
   - Import `/Database/db.sql`

3. **Configure DB connection:**  
   Open `/Database/db.php` and update if needed:  
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "a2b_delivery";
   ```

4. **Run project:**  
   ```
   http://localhost/A2B-Delivery/home.php
   ```

5. **Default admin credentials:**  
   ```
   Email: admin@example.com
   Password: admin123
   ```

---

## 🧪 Testing Checklist
✅ Login & registration for all roles  
✅ Create & accept delivery request  
✅ Live map rendering via Leaflet  
✅ Delivery status update flow  
✅ Report and analytics display  
✅ File uploads (driver license, profile photo)  
✅ Secure logout and session expiry  

---

## 📚 References
- [Leaflet.js Official Docs](https://leafletjs.com/)  
- [OpenStreetMap](https://www.openstreetmap.org/)  
- [Bootstrap 5 Documentation](https://getbootstrap.com/)  
- W3Schools — PHP & MySQL  
- CISA (2021) — Web application security guidelines  

---

## 🏁 Conclusion
The **A To B Delivery Service Website** effectively demonstrates secure, scalable, and interactive web development principles.  
By using **Leaflet.js** and **OpenStreetMap**, it delivers a modern, cost-free mapping feature suitable for local logistics startups and small-scale delivery providers.  

This project aligns with NCC Computing standards, showcasing practical implementation of full-stack concepts, database management, and real-time system functionality.

---

## 📄 License
This project is for academic and educational purposes only.  
Redistribution or commercial use without permission is prohibited.
