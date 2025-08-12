# 🏥 Healthcare Management System

Author: Team HealthBridge
Team Members (Full-Stack Contribution)
- Eshrath Jahan Esha — User Register & Login, Full Module — ID: 231-115-041
- Md. Abu Hasan — Blood Donation, Full Module — ID: 231-115-048
- Niyaz Ahmad Khan — User & Admin Dashboard, Medical Appointment, Full Module — ID: 231-115-052
- Shahriar Najim — Pharmacy Management & Marketplace, Full Module — ID: 231-115-078
Section: B
Batch: 58th

Project Demonstrating video : https://drive.google.com/file/d/1qTDbZqvyv5GKNq5j7sM7EoYJMRKNnwyR/view?usp=sharing

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

- ER Diagram
![image alt](https://github.com/theniyazkhan/HealthBridge/blob/12c92fe9b43e95ce6146bba9b50ef74dfc92c5c4/ER%20Diagram.png)

- Register Page
![image alt](https://github.com/theniyazkhan/HealthBridge/blob/d250dd9363d9b59b4e6c1950864fc9c99a617ba8/Register.jpg)

- Login Page
![image alt](https://github.com/theniyazkhan/HealthBridge/blob/e974c618806b2b416db036a6139a374372110b6a/login.jpg)


👨‍💻 Tech Stack
Frontend: HTML5, CSS3, JavaScript

Backend: PHP (Procedural + MySQLi)

Database: MySQL

Tools: XAMPP, phpMyAdmin

📜 License
© All right reserved 2025

