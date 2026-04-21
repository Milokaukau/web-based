# NOAIR - Water Bottle E-Commerce Platform

NOAIR is a custom-built web-based e-commerce platform specifically designed to sell premium water bottles. It features a complete shopping experience for users, along with a robust, multi-tiered administrative dashboard for managing products, tracking orders, and business analytics.

## 🚀 Features

### For Members
* **Authentication:** Registration, Login, and secure Password Reset (via email using PHPMailer).
* **Shopping Experience:** Browse products by categories, add to cart, and manage a personal wishlist.
* **Order Management:** Secure checkout process, payment status tracking, and order history.
* **Profile Customization:** Update personal details and upload avatars (supports direct webcam capture).

### For Administrators
* **Role-Based Access:** Standard `admin` and `superadmin` privileges.
* **Order & Payment Processing:** Update order fulfillment workflows and payment statuses independently.
* **Inventory Management:** Add, edit, and categorize products.
* **Data & Analytics:** Visual charts for revenue, top-selling products, and user-demographics.

## 🛠 Tech Stack

* **Backend:** PHP, PDO for secure database interactions.
* **Database:** MySQL.
* **Frontend:** HTML5, CSS3, JavaScript.
* **Dependencies:** Composer, PHPMailer (for transactional emails), Stripe (for fake payments).

## 📁 Project Structure

The project follows a modular, MVC-inspired directory structure to separate concerns:

* `/pages/` - Frontend views and UI layouts.
* `/logic/` - Business logic, form validation, and authentication routing.
* `/database/` - Direct database interaction scripts (SQL Queries).
* `/components/` - Reusable UI elements (Header, Footer, Navigation).
* `/css/` & `/js/` - For styling and frontend interactions.

## ⚙️ Setup Instructions

1. **Extract ZIP:** Extract the .zip folder of this project.
2. **Open VS Code:** Open the project in Visual Studio Code.
3. **Install dependencies:** Run `composer require stripe/stripe-php` and `composer require phpmailer/phpmailer`.
4. **Setup Stripe Secret Key:** Create an account in [Stripe](https://stripe.com/en-my) and use your own secret key for the project in `pages/payment_status.php` and `pages/payment.php`.
5. **Turn on XAMPP:** Start Apache and MySQL services.
6. **Import database:** Import the provided `db_noair.sql` file in phpMyAdmin.
7. **Change Port:** Change the port(s) in `/config.php` if your port for Apache or MySQL services is different.
8. **Run the project:** Run `php -S localhost:8000` in the project root in the terminal.

## Login Default Passwords
- **Member:** 12345678
- **Admin:** password
