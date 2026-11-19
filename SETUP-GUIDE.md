# StructZone - Setup & Installation Guide

## 📋 Project Overview

**StructZone** is a professional structural engineering company website with:
- Dynamic content management via Admin Panel
- Project portfolio with video support
- Services showcase
- Blog/Insights section
- Contact form with enquiry management
- Client testimonials

---

## 🛠️ Requirements

Before you begin, ensure you have the following installed:

| Software | Version | Download |
|----------|---------|----------|
| XAMPP | 8.0+ | https://www.apachefriends.org/ |
| Web Browser | Chrome/Firefox | - |
| Text Editor | VS Code (optional) | https://code.visualstudio.com/ |

---

## 📥 Installation Steps

### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Run the installer and follow the prompts
3. Install to default location (e.g., `C:\xampp`)

### Step 2: Extract Project Files

1. Extract the `structzone.zip` file
2. Copy the entire `structzone` folder to:
   - **Windows:** `C:\xampp\htdocs\structzone`
   - **Mac:** `/Applications/XAMPP/htdocs/structzone`

### Step 3: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache**
3. Click **Start** next to **MySQL**
4. Both should show green "Running" status

### Step 4: Create Database

1. Open your browser and go to: http://localhost/phpmyadmin
2. Click **"New"** in the left sidebar
3. Enter database name: `structzone_db`
4. Click **"Create"**

### Step 5: Import Database Schema

1. In phpMyAdmin, click on `structzone_db` database
2. Click the **"Import"** tab at the top
3. Click **"Choose File"** and select:
   `structzone/admin/schema.sql`
4. Scroll down and click **"Go"**
5. Wait for "Import has been successfully finished" message

### Step 6: Configure Database (if needed)

If your MySQL has a password, edit the configuration file:

**File:** `structzone/includes/config.php`

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'structzone_db');
define('DB_USER', 'root');          // Change if different
define('DB_PASS', '');              // Add password if set
```

---

## 🚀 Running the Website

### Option A: Using PHP Built-in Server (Recommended)

1. Open **Command Prompt** or **PowerShell**
2. Navigate to the project folder:
   ```
   cd C:\xampp\htdocs\structzone
   ```
3. Start the server:
   ```
   C:\xampp\php\php.exe -S localhost:8000
   ```
4. Open browser: http://localhost:8000

### Option B: Using XAMPP Apache

1. Ensure Apache is running in XAMPP
2. Open browser: http://localhost/structzone

---

## 🔐 Admin Panel Access

| Field | Value |
|-------|-------|
| **URL** | http://localhost:8000/admin |
| **Email** | admin@structzone.com |
| **Password** | Admin@123 |

### Admin Features:
- ✅ Dashboard with statistics
- ✅ Manage Projects (with video support)
- ✅ Manage Services
- ✅ Manage Insights/Blog
- ✅ Manage Clients/Testimonials
- ✅ View Contact Enquiries
- ✅ Site Settings

---

## 📁 Project Structure

```
structzone/
├── admin/              # Admin panel files
│   ├── assets/         # Admin CSS/JS
│   ├── includes/       # Admin PHP includes
│   ├── uploads/        # Uploaded files
│   └── schema.sql      # Database structure
├── assets/             # Frontend assets
│   ├── css/            # Stylesheets
│   ├── images/         # Images
│   └── videos/         # Video files
├── includes/           # Frontend PHP includes
├── projects/           # Project pages
├── services/           # Service pages
├── insights/           # Blog pages
├── index.php           # Homepage
├── about.php           # About page
├── contact.php         # Contact page
└── README.md           # This file
```

---

## ⚠️ Troubleshooting

### "Cannot connect to database"
- Ensure MySQL is running in XAMPP
- Check database name matches `structzone_db`
- Verify credentials in `includes/config.php`

### "Page not found" errors
- Make sure you're using correct URL
- Check if Apache/PHP server is running

### Videos not playing
- Ensure video files exist in `assets/videos/`
- Check browser console for errors
- Videos must be .mp4 format

### Admin login not working
- Clear browser cookies
- Verify credentials: `admin@structzone.com` / `Admin@123`
- Re-import `admin/schema.sql` if needed

---

## 📞 Support

For technical assistance, contact the developer.

---

**Version:** 1.0  
**Last Updated:** February 2026
