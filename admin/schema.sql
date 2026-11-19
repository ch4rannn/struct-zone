-- StructZone Admin Dashboard - Database Schema
-- Run this SQL in your MySQL database

-- =============================================
-- Users Table
-- =============================================
CREATE TABLE IF NOT EXISTS sz_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Homepage Settings
-- =============================================
CREATE TABLE IF NOT EXISTS sz_homepage (
    id INT PRIMARY KEY DEFAULT 1,
    hero_heading VARCHAR(255) DEFAULT 'Engineering Excellence',
    hero_subheading TEXT,
    cta_text VARCHAR(100) DEFAULT 'Get a Quote',
    cta_link VARCHAR(255) DEFAULT '/contact.php',
    hero_background VARCHAR(500),
    show_services TINYINT(1) DEFAULT 1,
    show_projects TINYINT(1) DEFAULT 1,
    show_testimonials TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default homepage row
INSERT INTO sz_homepage (id) VALUES (1) ON DUPLICATE KEY UPDATE id=1;

-- =============================================
-- Services
-- =============================================
CREATE TABLE IF NOT EXISTS sz_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'building',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Projects
-- =============================================
CREATE TABLE IF NOT EXISTS sz_projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    category ENUM('Commercial','Residential','Industrial','Infrastructure','Institutional') DEFAULT 'Commercial',
    location VARCHAR(255),
    short_desc TEXT,
    case_study LONGTEXT,
    featured_image VARCHAR(500),
    is_featured TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Project Images Gallery
-- =============================================
CREATE TABLE IF NOT EXISTS sz_project_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    caption VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES sz_projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Insights / Blog
-- =============================================
CREATE TABLE IF NOT EXISTS sz_insights (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    content LONGTEXT,
    excerpt TEXT,
    featured_image VARCHAR(500),
    meta_title VARCHAR(255),
    meta_description TEXT,
    author_id INT,
    status ENUM('draft', 'published') DEFAULT 'draft',
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES sz_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- About Page
-- =============================================
CREATE TABLE IF NOT EXISTS sz_about (
    id INT PRIMARY KEY DEFAULT 1,
    intro_text TEXT,
    experience_years INT DEFAULT 10,
    values_text TEXT,
    mission_text TEXT,
    vision_text TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default about row
INSERT INTO sz_about (id) VALUES (1) ON DUPLICATE KEY UPDATE id=1;

-- =============================================
-- Founders / Team
-- =============================================
CREATE TABLE IF NOT EXISTS sz_founders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100),
    bio TEXT,
    image VARCHAR(500),
    linkedin VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Contact Enquiries
-- =============================================
CREATE TABLE IF NOT EXISTS sz_enquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    company VARCHAR(255),
    service VARCHAR(100),
    message TEXT,
    status ENUM('new', 'in_progress', 'closed') DEFAULT 'new',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Site Settings
-- =============================================
CREATE TABLE IF NOT EXISTS sz_settings (
    id INT PRIMARY KEY DEFAULT 1,
    site_name VARCHAR(255) DEFAULT 'StructZone',
    tagline VARCHAR(255) DEFAULT 'Engineering Stability. Designing the Future.',
    logo VARCHAR(500),
    favicon VARCHAR(500),
    footer_text TEXT,
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    linkedin VARCHAR(255),
    instagram VARCHAR(255),
    youtube VARCHAR(255),
    whatsapp VARCHAR(50),
    contact_email VARCHAR(255),
    contact_phone VARCHAR(50),
    contact_address TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default settings row
INSERT INTO sz_settings (id) VALUES (1) ON DUPLICATE KEY UPDATE id=1;

-- =============================================
-- Activity Log (Optional - for tracking changes)
-- =============================================
CREATE TABLE IF NOT EXISTS sz_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    entity_type VARCHAR(50),
    entity_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES sz_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
