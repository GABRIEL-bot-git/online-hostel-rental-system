CUSTECH Off-Campus Hostel Rental System
An escrow-based, secure web platform designed to streamline student accommodation management, eliminate housing fraud, and facilitate verified property transactions in Osara and the Lokoja metropolis.

📌 Abstract & Overview
The CUSTECH Hostel Rental System addresses the critical inefficiencies and security vulnerabilities inherent in traditional off-campus housing allocation. By centralizing property listings and integrating a rigorous Know Your Customer (KYC) framework, this system provides a secure ecosystem for students, landlords, and university administrators.

The platform operates on a modernized escrow architecture. Student payments are securely routed via the Paystack API, temporarily held, and credited to the respective landlord's virtual wallet. This ensures financial accountability, enables structured refund protocols, and mitigates the risk of fraudulent property listings.

🚀 Core Features
🎓 Student Module
Advanced Search & Filtering: Filter accommodations by proximity, pricing, and property type across Osara and Lokoja.

Secure Checkout: Integrated Paystack payment gateway for real-time transaction processing.

Automated Refund Architecture: Built-in protocol allowing students to request transaction cancellations and refunds.

Dynamic Receipt Generation: Downloadable, printable receipts featuring dynamic voiding logic for cancelled or refunded bookings.

🏢 Landlord & Agent Module
Geospatial Property Mapping: Integration with Leaflet.js and OpenStreetMap for precise property pinpointing.

KYC & Identity Verification: Mandatory National Identity Number (NIN) collection to establish accountability.

Virtual Escrow Wallet: Real-time financial dashboard tracking booking revenues and available withdrawal balances.

Automated Payouts: Secure interface for requesting fund withdrawals directly to specified bank accounts.

🔐 Administrative Control Panel
Property Vetting: Manual approval queue for all newly uploaded properties to ensure quality and authenticity.

Financial Oversight: Centralized dashboard to approve student refund requests and process landlord withdrawal disbursements.

Transaction Auditing: Complete visibility into the system's ledger, tracking all successful, pending, and reversed transactions.

💻 System Architecture & Technology Stack
The application is engineered using a robust Three-Tier Client-Server Architecture, ensuring clear separation of concerns, scalability, and maintainability.

Presentation Tier (Frontend): HTML5, CSS3, Bootstrap 5, JavaScript, jQuery, Leaflet.js

Application Logic Tier (Backend): PHP 8.x (Procedural/OOP hybrid)

Data Tier (Database): MySQL (Relational Database Management System)

External APIs: Paystack API (Payment Processing)

🔒 Cybersecurity & Data Integrity Measures
Cryptographic Hashing: All user passwords are encrypted using PHP's native password_hash() algorithm (Bcrypt).

Escrow Financial Routing: Direct peer-to-peer transactions are restricted. Funds are governed by administrative logic before reaching landlord wallets.

Input Sanitization: Global implementation of $conn->real_escape_string() to mitigate SQL injection vulnerabilities.

Strict Session Management: Role-based access control (RBAC) ensures students, landlords, and admins are strictly confined to their authorized environments.

⚙️ Installation & Deployment Protocol
Follow these steps to deploy the system locally for development or testing:

1. Prerequisites
Ensure you have a local server environment configured (e.g., XAMPP, WAMP, or MAMP) running PHP 7.4+ and MySQL.

2. Repository Cloning
Bash
git clone [https://github.com/GABRIEL-bot-git/custech-hostel-system](https://github.com/GABRIEL-bot-git/online-hostel-rental-system).git
cd hostel-system
3. Database Configuration
Open phpMyAdmin (or your preferred database manager).

Create a new, empty database named hostel_system.

Import the provided SQL schema: database/hostel_system.sql.

4. Application Configuration
Navigate to the includes/ directory.

Open db_connect.php and configure your database credentials:

PHP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hostel_system";
(Optional) Insert your active Paystack Public/Secret keys into the payment verification controllers if deploying for live transactions.

5. Initialization
Move the project folder into your server's root directory (e.g., htdocs/ for XAMPP).

Launch the application via your web browser: http://localhost/custech-hostel-system/.

Register a new Admin, Landlord, and Student to begin simulating the escrow lifecycle.

📜 License
This project was developed for academic purposes at Confluence University of Science and Technology (CUSTECH). Educational use, modification, and distribution are permitted.
