# 🎮 NexGenPlayz - Online Game Distribution Platform

![NexGenPlayz Logo](img/2.png)

**NexGenPlayz** is a full-stack web application designed as a digital video game distribution store. Built with a futuristic "Cyberpunk/Neon" aesthetic, it features a complete user ecosystem for buying and managing games, alongside a robust administrator dashboard for content management.

This project was developed as a final year college project (BCA) to demonstrate core web development capabilities including session management, database normalization, and CRUD operations.

---

## ✨ Features

### 👤 User Features
* **Authentication:** Secure Registration and Login system.
* **Dynamic Storefront:** Browse available games with dynamic cover art and auto-playing video previews fetched from the database.
* **Profile Management:** * **"My Games" Library:** Displays all purchased games in a dedicated grid.
    * **Stats Tracking:** Automatically tracks session duration to calculate "Hours Logged" upon logout.
    * **Edit Profile:** Update username and profile picture.
* **Purchase Simulation:** Mock Payment Gateway supporting Debit Card inputs with validation.
* **Support System:** Integrated contact form for submitting inquiries directly to the database.

### 🛡️ Admin Panel
* **Dashboard Access:** Dedicated login portal for administrators.
* **Game Management (CRUD):** * **Add Games:** Upload titles, prices, cover images, and video previews.
    * **Edit Games:** Update existing game details via a modal interface.
    * **Delete Games:** Remove games and automatically cascade deletions for related user ownership and payment records.
* **User Support:** View and delete customer support messages.
* **Admin Management:** Capability to register new administrators.

---

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3 (Glassmorphism, Grid/Flexbox, Keyframe Animations), JavaScript.
* **Backend:** PHP (PDO Extension for database security).
* **Database:** MySQL (Relational Schema).
* **Server Environment:** XAMPP / Apache.

---

## 📂 Database Schema

The project uses a normalized database named `nexgenplayz_db` containing the following tables:

1.  **`users`**: Stores user credentials, profile paths, and taglines.
2.  **`admins`**: Stores administrator credentials.
3.  **`games`**: Stores game metadata (title, price, file paths).
4.  **`user_owned_games`**: Links users to the games they have purchased.
5.  **`user_stats`**: Tracks gameplay hours and rank data.
6.  **`payments`**: Logs transaction details (card info simulation).
7.  **`support_messages`**: Stores user inquiries.

---

## 🚀 Installation & Setup

### 1. Prerequisites
Ensure you have a local server environment installed (e.g., **XAMPP**, **WAMP**, or **MAMP**).

### 2. Database Configuration
1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Create a new database named: `nexgenplayz_db`.
3.  Import the provided SQL file: `nexgenplayz_db.sql`.
    * *This file contains the table structure and seed data.*

### 3. Server Configuration
1.  Copy the project folder to your server root (e.g., `htdocs`).
2.  Verify `db_connect.php` settings match your environment:
    ```php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nexgenplayz_db";
    ```

### 4. Run the Project
Open your browser and navigate to: `http://localhost/NexGenPlayz/index.php`.

---

## 🔑 Default Credentials

You can use the data pre-loaded in `nexgenplayz_db.sql` to test the application immediately.

### Administrator Login
* **URL:** `Admin_Login.php`
* **Email:** `JENIL@gmail.com`
* **Password:** `102030`

### User Login
* **URL:** `Login.php`
* **Email:** `jenil@gmail.com`
* **Password:** `JENIL`

---

## 📂 Project Structure

* **`Core`**: `db_connect.php` (DB Connection), `index.php` (Router/Redirect).
* **`Auth`**: `Login.php`, `register.php`, `login_action.php`, `logout.php`, `Admin_Login.php`.
* **`User Views`**: 
    * `Home.php` (Landing page).
    * `Games.php` (Catalog).
    * `Profile.php` (User dashboard).
    * `edit_profile.php` (Profile settings).
    * `Support.php` (Contact form).
    * `About.php` (Info page).
* **`Commerce`**: 
    * `PaymentGateway.php` (Checkout UI).
    * `process_purchase.php` (Transaction logic).
    * `purchase_success.php` (Confirmation).
* **`Assets`**: `/img` (Images), `/video` (Game previews), `/uploads` (User avatars).

---

## 📝 Developer Notes

* **Session Tracking:** The `logout.php` script calculates the difference between login time and logout time to update the `hours_logged` in the `user_stats` table.
* **UI Effects:** The site utilizes extensive CSS animations (e.g., `view()` timelines) for scroll-triggered effects in files like `Home.php` and `About.php`.

---

*Developed by [Your Name] | 2025*
