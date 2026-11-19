<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+"/>
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
  <img src="https://img.shields.io/badge/GSAP-Animations-88CE02?style=for-the-badge&logo=greensock&logoColor=white" alt="GSAP"/>
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License"/>
</p>

<h1 align="center">🏗️ StructZone</h1>

<p align="center">
  <strong>Engineering Stability. Designing the Future.</strong><br/>
  Professional structural engineering company website with a full-featured admin panel, project portfolio, blog, and contact management.
</p>

<p align="center">
  <a href="https://www.structzone.com">🌐 Live Site</a> •
  <a href="#-features">✨ Features</a> •
  <a href="#-quick-start">🚀 Quick Start</a> •
  <a href="#-admin-panel">🔐 Admin Panel</a> •
  <a href="#-deployment">☁️ Deployment</a>
</p>

---

## 📸 Overview

StructZone is a production-ready website built for a structural engineering consultancy serving clients across India. It features a cinematic video hero, dynamic service pages, a project portfolio with video walkthroughs, an insights/blog section, and a fully integrated admin dashboard — all built with **zero frameworks**, just clean PHP, vanilla JavaScript, and GSAP animations.

> **Live at:** [www.structzone.com](https://www.structzone.com)

---

## ✨ Features

### 🌐 Public Website
- **Cinematic Hero** — Full-screen video/image/YouTube background with auto-detection
- **Dynamic Services** — CMS-driven service pages with custom icons and slugs
- **Project Portfolio** — Filterable gallery with video carousel and case studies
- **Insights / Blog** — SEO-optimized articles with rich content
- **Client Testimonials** — Social proof section with avatars
- **Contact Form** — Enquiry form with WhatsApp integration
- **SEO Ready** — Sitemap, `robots.txt`, meta tags, canonical URLs, Open Graph

### 🔐 Admin Panel
- **Dashboard** — At-a-glance stats for projects, services, enquiries
- **Content Management** — Full CRUD for projects, services, insights, and clients
- **Homepage Editor** — Change hero content, CTAs, and backgrounds from the panel
- **Team Management** — Add/edit founders with bios, photos, and LinkedIn links
- **Enquiry Inbox** — View, track, and manage contact form submissions
- **Site Settings** — Update branding, social links, and contact info
- **Media Uploads** — Image and video upload with file management
- **Auth System** — Secure login with password reset flow

### ⚡ Performance & Design
- **GSAP Animations** — Subtle, scroll-triggered fade-ins and reveals
- **Fully Responsive** — Mobile-first CSS, works across all devices
- **No Build Step** — Pure HTML/CSS/JS + PHP — deploy anywhere, no bundlers
- **Lazy Loading** — Images and videos load on demand for fast page loads

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Animations** | GSAP (GreenSock) |
| **Backend** | PHP 8.2+ (no framework) |
| **Database** | MySQL / MariaDB |
| **Containerization** | Docker + Apache |
| **Deployment** | Render, cPanel, or any PHP host |

---

## 🚀 Quick Start

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) 8.0+ (or any PHP 8.x + MySQL setup)
- A modern web browser

### 1. Clone the repository

```bash
git clone https://github.com/ch4rannn/struct-zone.git
cd struct-zone
```

### 2. Create the database

Open [phpMyAdmin](http://localhost/phpmyadmin) and create a new database:

```sql
CREATE DATABASE structzone_db;
```

### 3. Import the schema

```bash
# Via phpMyAdmin: Import → Choose File → admin/schema.sql → Go
# Or via CLI:
mysql -u root structzone_db < admin/schema.sql
```

### 4. Configure the connection

Edit [`includes/config.php`](includes/config.php) if your MySQL credentials differ from the defaults:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'structzone_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 5. Start the server

```bash
# Option A: PHP built-in server (recommended for development)
php -S localhost:8000

# Option B: XAMPP Apache
# Copy project to C:\xampp\htdocs\structzone → open http://localhost/structzone
```

### 6. Open the site

```
Frontend  →  http://localhost:8000
Admin     →  http://localhost:8000/admin
```

---

## 🔐 Admin Panel

| Field | Value |
|-------|-------|
| **URL** | `/admin` |
| **Email** | `admin@structzone.com` |
| **Password** | `Admin@123` |

From the admin panel you can manage:

| Module | Capabilities |
|--------|-------------|
| **Dashboard** | View project/service/enquiry counts at a glance |
| **Projects** | Add, edit, delete — with video uploads & case studies |
| **Services** | Manage service offerings with icons and descriptions |
| **Insights** | Publish blog articles with rich content and images |
| **Clients** | Manage testimonials and client logos |
| **Enquiries** | View and track contact form submissions |
| **Homepage** | Edit hero content, CTAs, and media backgrounds |
| **Settings** | Update site branding, social links, and contact details |

---

## 📁 Project Structure

```
structzone/
├── admin/                  # Admin panel
│   ├── assets/             #   Admin-specific CSS & JS
│   ├── includes/           #   Admin PHP logic (auth, DB)
│   ├── uploads/            #   Uploaded media files
│   ├── schema.sql          #   Database schema
│   ├── dashboard.php       #   Admin dashboard
│   ├── projects.php        #   Project management
│   ├── services.php        #   Service management
│   ├── insights.php        #   Blog management
│   ├── clients.php         #   Testimonial management
│   ├── enquiries.php       #   Contact enquiry inbox
│   └── settings.php        #   Site configuration
│
├── assets/                 # Frontend assets
│   ├── css/main.css        #   All styles (~66KB)
│   ├── js/main.js          #   Core JavaScript
│   ├── js/animations.js    #   GSAP scroll animations
│   ├── images/             #   Static images
│   └── videos/             #   Hero & project videos
│
├── includes/               # Shared PHP includes
│   ├── config.php          #   Database & site config
│   ├── header.php          #   HTML head & navigation
│   ├── footer.php          #   Footer & scripts
│   ├── site-data.php       #   Data access layer
│   ├── service-template.php#   Dynamic service page renderer
│   └── project-template.php#   Dynamic project page renderer
│
├── services/               # Service pages (dynamic routing)
├── projects/               # Project listing & detail views
├── insights/               # Blog listing page
├── uploads/                # User-uploaded project media
│
├── index.php               # Homepage
├── about.php               # About page
├── contact.php             # Contact page
├── process-contact.php     # Form submission handler
│
├── Dockerfile              # Docker containerization
├── render.yaml             # Render.com deployment config
├── sitemap.xml             # SEO sitemap
├── robots.txt              # Search engine directives
└── SETUP-GUIDE.md          # Detailed installation guide
```

---

## ☁️ Deployment

### Docker

```bash
docker build -t structzone .
docker run -p 8080:80 \
  -e DB_HOST=your-db-host \
  -e DB_NAME=structzone_db \
  -e DB_USER=your-user \
  -e DB_PASS=your-password \
  -e SITE_URL=https://yourdomain.com \
  structzone
```

### Render.com

The repo includes a [`render.yaml`](render.yaml) for one-click deployment on Render:

1. Connect the GitHub repo to Render
2. Set environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `SITE_URL`)
3. Deploy — the Docker build runs automatically

### cPanel / Shared Hosting

1. Upload all files to `public_html/`
2. Create a MySQL database and import `admin/schema.sql`
3. Update `includes/config.php` with your credentials
4. Enable PHP 8.1+, HTTPS, and GZIP compression

---

## 🗄️ Database Schema

The application uses **10 tables** prefixed with `sz_`:

| Table | Purpose |
|-------|---------|
| `sz_users` | Admin authentication & roles |
| `sz_homepage` | Hero section & homepage settings |
| `sz_services` | Engineering service offerings |
| `sz_projects` | Project portfolio entries |
| `sz_project_images` | Project image galleries |
| `sz_insights` | Blog / insights articles |
| `sz_about` | About page content |
| `sz_founders` | Team member profiles |
| `sz_enquiries` | Contact form submissions |
| `sz_settings` | Global site configuration |
| `sz_activity_log` | Admin action audit trail |

> Full schema: [`admin/schema.sql`](admin/schema.sql)

---

## 🧰 Engineering Tools Referenced

The site showcases expertise with industry-standard structural engineering software:

- **ETABS & SAP2000** — Structural analysis
- **STAAD.Pro** — Steel & concrete design
- **Revit & Tekla** — BIM modeling
- **AutoCAD** — Structural drafting

Compliant with **IS 456**, **IS 800**, **IS 1893** (Seismic), **IS 875**, **ACI**, and **AISC** standards.

---

## 🤝 Contributing

Contributions are welcome! Feel free to open issues or submit pull requests.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

## 📞 Contact

**StructZone** — Structural Engineering Consultancy

- 🌐 Website: [www.structzone.com](https://www.structzone.com)
- 📧 Email: [info@structzone.com](mailto:info@structzone.com)
- 📱 Phone: +91 6201418611
- 💼 LinkedIn: [linkedin.com/company/structzone](https://linkedin.com/company/structzone)
- 📍 Location: Sahibzada Ajit Singh Nagar, Punjab, India

---

<p align="center">
  <sub>Built with ❤️ by the StructZone team</sub>
</p>
