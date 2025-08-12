<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Dashboard | HealthBridge</title>
  <style>
 
    /* Top-right buttons */
    .top-buttons {
      position: absolute;
      top: 15px;
      right: 20px;
      display: flex;
      gap: 8px;
    }
    .top-buttons a {
      padding: 8px 14px;
      border-radius: 6px;
      font-size: 0.9rem;
      text-decoration: none;
      font-weight: 500;
      background:  #18a88b;
      color: white;
      /* border: 1px solid #18a88b; */
      transition: background 0.3s ease, color 0.3s ease;
    }
    .top-buttons a:hover {
      background: white;
      color: #18a88b;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f8ff;
      margin: 0;
      padding: 0;
      width: 100%;

    }

    header {
      background: #23cba7ff;
      color: white;
      padding: 20px;
      text-align: center;
      /* width: 100%; */
    }
    header.header-area {
    background-size: cover;
    background-repeat: no-repeat;
}
    .header-area h1{
        color: #23cba7ff;
    }
    .header-area h1 span{
        color:black;
    
    }
    .header-area p{
        color: #00000085;
        font-weight: 500;
    }

    .dashboard {
      display: flex;
      justify-content: space-around;
      align-items: center;
      margin: 40px auto;
      max-width: 1000px;
      gap: 40px;
    }

    .feature {
      flex: 1;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
      padding: 30px;
      transition: 0.3s;
      position: relative;
      cursor: pointer;
      margin-top: 50px;
    }

    .feature:hover {
      /* transform: translateY(-10px); */
      transform: scale(1.05);
      background: #e0f7f4;
      border: 2px solid #32d4cfff;
      /* border-radius: 20px 0 20px 0; */
    }

    .feature.active::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: #23cba7;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
    }

    .feature img {
    width: 100px;
    /* height: 60px; */
    margin-bottom: 15px;
}

    .register-donor {
      text-align: center;
      margin-top: 100px;
      font-size: 1.2rem;
      color: red;
    }
    .register-donor a {
        padding: 15px 10px;
        background-color: black;
        border-radius: 10px;
        text-decoration: none;
        color: white;
        background:  #18a88b;
      color: white;
      /* border: 1px solid #18a88b; */
      transition: background 0.3s ease, color 0.3s ease;
    }

    
    .register-donor a:hover {
      background: white;
      color: #18a88b;
      border: 1px solid #18a88b;
    }
    .profile-box {
      text-align: center;
      margin-top: 40px;
    }

    .profile-box a {
      color: #23cba7;
      text-decoration: none;
      font-weight: bold;
    }

    /* Footer */
    footer {
      background: linear-gradient(360deg, #18a88b, #23cba7);
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 0.95rem;
      /* width: 100%; */
      /* height: 50px; */
      margin-top: 150px;
      box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
    }
    footer small {
      display: block;
      margin-top: 5px;
      font-size: 0.8rem;
      opacity: 0.85;
    }
  </style>
</head>
<body>

<header class="header-area"  style="background-image: url('../pharmacy/images/bg.jpg');">
  <div class="top-buttons">
    <!-- <a href="../appointment/edit_profile.php">Edit Profile</a> -->
    <a href="../logout.php">Logout</a>
  </div>
  <h1><span>Welcome</span> to HealthBridge</h1>
  <p>Your one-stop solution for better health & care</p>
</header>

<section class="dashboard-area">
    <div class="dashboard">
  <div class="feature" onclick="window.location.href='../appointment/book.php'">
    <img src="../images/doctor.png" alt="Appointment">
    <h3>Book Appointment</h3>
  </div>

  <div class="feature" onclick="window.location.href='../blood_donation/request_blood.php'">
    <img src="../images/blood.png" alt="Blood Donation">
    <h3>Request Blood</h3>
  </div>

  <div class="feature" onclick="window.location.href='../pharmacy/search_medicine.php'">
    <img src="../images/pharmacy.png" alt="Pharmacy">
    <h3>Pharmacy</h3>
  </div>
</div>

<div class="register-donor">
   <a  href="../blood_donation/register_donor.php">Become a Donor</a>
</div>
</section>




<footer>
  &copy; 2025 HealthBridge. All Rights Reserved.
  <small>For better healthcare experiences</small>
</footer>

</body>
</html>
