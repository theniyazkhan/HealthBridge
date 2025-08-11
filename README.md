# 🏥 Healthcare Management System

A **full-stack healthcare web application** with three core modules:
1. **Medical Appointment System**
2. **Blood Donation System**
3. **Pharmacy Management System**

Built with:
- **Frontend:** HTML, CSS (custom + responsive)
- **Backend:** PHP
- **Database:** MySQL (via XAMPP)

This project provides **separate admin panels** for each module, user authentication, appointment booking, donor management, pharmacy inventory handling, and more.

---

## ✨ Features

### 🔹 Common Features
- Secure **user login & registration** system.
- **Admin and user dashboards** with role-based access.
- Responsive **modern UI** with gradient headers, animations, and clean design.
- Search and filter functionalities.
- MySQL database integration with foreign key relationships.

### 🔹 Medical Appointment System
- Book appointments with doctors.
- View **My Appointments** page (pulls data from `appointments`, `doctors`, and `users` tables).
- Search doctors by name or specialization.
- Doctors manages patients and appointments.

### 🔹 Blood Donation System
- Donor registration form.
- Blood request form.
- Search donors by name, blood type, or location and filter eligible donors.
- Admin panel for managing donor data and blood stock.

### 🔹 Pharmacy Management System
- Manage medicines and inventory.
- Search medicines by name or category.
- Admin panel for stock updates.
- Users can view available medicines and purchase requests.

---

## 📂 Project Structure
healthcare_system/
│
├── appointment/
│ ├── admin.php
│ ├── book.php
│ ├── dashboard_doctor.php
│ ├── dashboard_patient.php
│ ├── my_appointments.php
│ └── edti_profile.php
│
├── blood_donation/
│ ├── uploads
│ ├── blood_admin.php
│ ├── register_donor.php
│ ├── request_blood.php
│ └── search_donor.php
│
├── pharmacy/
│ ├── images/
│ ├── cart.php
│ ├── search_medicine.php
│ └── admin_panel.php
│
├── includes/
│ ├── db.php
│ ├── header.php
│ ├── footer.php
│
├── css/
│ ├── style.css
│ ├── blood.css
│ ├── doctor_dashboardr.css
│ ├── doctor.css
│ ├── edit_profile.css
│ ├── user.css
│
├── images/
│
├── index.php
├── login.php
├── logoout.php
├── register.php
├── stlyle.css
│
└── README.md


## ⚙️ Installation & Setup

### 1️⃣ Prerequisites
- [XAMPP](https://www.apachefriends.org/index.html) (or any PHP + MySQL environment)
- PHP 7.4+ recommended
- MySQL

### 2️⃣ Steps
1. **Clone the repository**
   ```bash
   git clone https://github.com/theniyazkhan/healthbridge.git
Move to XAMPP htdocs

Place the folder inside:
C:/xampp/htdocs/

Import the database

Open phpMyAdmin: http://localhost/phpmyadmin

Create a database:

CREATE DATABASE healthcare_db;
Import healthcare_db.sql (provided in project folder).

Update DB credentials

Edit includes/db.php and update:

$servername = "localhost";
$username = "root"; // your MySQL username
$password = ""; // your MySQL password
$dbname = "healthcare_db";
Run the project

Visit:
http://localhost/healthcare_system/

🖼️ Screenshots
(Add screenshots of home page, dashboards, and forms)

👨‍💻 Tech Stack
Frontend: HTML5, CSS3, JavaScript

Backend: PHP (Procedural + MySQLi)

Database: MySQL

Tools: XAMPP, phpMyAdmin

📜 License
All right reserved 

🤝 Contribution
Pull requests are welcome!
For major changes, open an issue first to discuss your idea.

📧 Contact
Author: Team HealthBridge
Team members:
Eshrath Jahan Esha, ID: (231-115-041)
Md.Abu Hasan, ID: (231-115-048)
Niyaz Ahmad Khan, ID: (231-115-052)
Shahriar Najim, ID: (231-115-078)
Section: B, Batch: 58th

