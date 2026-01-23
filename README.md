# 🏨 Student Hostel & Off-Campus Rental System

## 📖 Project Overview
This is a web-based platform designed to bridge the gap between students and landlords. It allows students to search, view, and securely book off-campus accommodation or school hostels. It includes a complete **Admin Dashboard** for verification and integrates **Paystack** for electronic payments.

## 🚀 Key Features
* **Student Module:** Search hostels by location, view images/prices, and make secure payments.
* **Landlord Module:** Upload property details, manage listings, and track bookings.
* **Admin Module:** Approve/Reject property listings and monitor platform activity.
* **Payment Integration:** Real-time payments using Paystack API.

## 🛠️ Technology Stack
* **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript (jQuery)
* **Backend:** PHP (Native)
* **Database:** MySQL
* **Payment Gateway:** Paystack

## ⚙️ Installation Guide (How to Run)
1.  **Clone the Repo:**
    ```bash
    git clone [https://github.com/GABRIEL-bot-git/online-hostel-rental-system.git](https://github.com/GABRIEL-bot-git/online-hostel-rental-system.git)
    ```
2.  **Database Setup:**
    * Open **phpMyAdmin**.
    * Create a database named `hostel_system`.
    * Import the `hostel_system.sql` file provided in this folder.
3.  **Configuration:**
    * Open `includes/db_connect.php` to ensure DB credentials match your local setup.
    * **Important:** Open `property_details.php` and replace the placeholder with your **Paystack Public Key**.
4.  **Run:**
    * Move the folder to `C:\xampp\htdocs\`
    * Open browser: `http://localhost/hostel_system`

## 🔐 Security Measures
* **SQL Injection Prevention:** Uses Prepared Statements.
* **XSS Protection:** Input sanitization on all forms.
* **Session Management:** Secure authentication logic.

## 👤 Author
**ANUOLUWAPO GABRIEL OGUNDIJO** *Final Year Project - Computer Science Department*