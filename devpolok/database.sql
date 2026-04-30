-- ============================================
-- DevPolok Portfolio CMS - Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS devpolok_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE devpolok_portfolio;

-- ============================================
-- ADMIN USERS
-- ============================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username=admin, password=admin123
INSERT INTO admin_users (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hasibulpolok.bdn@gmail.com');

-- ============================================
-- SITE SETTINGS
-- ============================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_title', 'DevPolok | Full Stack Developer'),
('site_tagline', 'Building Digital Experiences That Matter'),
('site_logo', ''),
('site_favicon', ''),
('site_description', 'Professional Full Stack Developer specializing in PHP, MySQL, JavaScript and modern web technologies.'),
('site_keywords', 'full stack developer, php developer, web developer, Bangladesh'),
('hero_title', 'Hi, I\'m <span class="highlight">Hasibul Polok</span>'),
('hero_subtitle', 'Full Stack Developer'),
('hero_description', 'I craft scalable, high-performance web applications with clean code and pixel-perfect UI. Let\'s build something extraordinary together.'),
('hero_cta_primary', 'View My Work'),
('hero_cta_secondary', 'Hire Me'),
('about_title', 'About Me'),
('about_bio', 'I am Hasibul Polok (DevPolok), a passionate Full Stack Developer based in Bangladesh with expertise in PHP, MySQL, JavaScript, and modern CSS frameworks. I love turning complex problems into elegant, efficient solutions. With a strong eye for design and a deep understanding of backend systems, I deliver projects that are both beautiful and robust.'),
('about_image', ''),
('about_experience', '3+'),
('about_projects', '50+'),
('about_clients', '30+'),
('services_title', 'What I Offer'),
('skills_title', 'My Tech Stack'),
('projects_title', 'Featured Projects'),
('contact_title', 'Let\'s Work Together'),
('contact_email', 'hasibulpolok.bdn@gmail.com'),
('contact_whatsapp', '01765967395'),
('contact_location', 'Bangladesh'),
('footer_text', '© 2025 DevPolok. Crafted with ❤️ in Bangladesh.'),
('social_github', 'https://github.com/hasibulpolok'),
('social_linkedin', 'https://www.linkedin.com/in/devpolok'),
('social_instagram', 'https://www.instagram.com/hasibulpolok.bdn/'),
('social_fiverr', 'https://www.fiverr.com/developerpolok'),
('color_primary', '#6366f1'),
('color_accent', '#f59e0b'),
('dark_mode_default', 'dark'),
('typing_words', 'Full Stack Developer,PHP Expert,UI/UX Enthusiast,Problem Solver');

-- ============================================
-- NAVIGATION MENU
-- ============================================
CREATE TABLE IF NOT EXISTS nav_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO nav_menu (title, url, sort_order, is_active) VALUES
('Home', '#home', 1, 1),
('About', '#about', 2, 1),
('Skills', '#skills', 3, 1),
('Services', '#services', 4, 1),
('Projects', '#projects', 5, 1),
('Contact', '#contact', 6, 1);

-- ============================================
-- SKILLS
-- ============================================
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    percentage INT DEFAULT 0,
    category VARCHAR(50) DEFAULT 'Frontend',
    icon VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO skills (name, percentage, category, icon, sort_order) VALUES
('HTML5', 95, 'Frontend', 'html5', 1),
('CSS3', 90, 'Frontend', 'css3', 2),
('JavaScript', 85, 'Frontend', 'javascript', 3),
('Tailwind CSS', 88, 'Frontend', 'tailwind', 4),
('PHP', 90, 'Backend', 'php', 5),
('MySQL', 85, 'Backend', 'mysql', 6),
('Git & GitHub', 80, 'Tools', 'git', 7),
('Bootstrap', 85, 'Frontend', 'bootstrap', 8);

-- ============================================
-- SERVICES
-- ============================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO services (title, description, icon, sort_order) VALUES
('Web Development', 'Full-stack web applications built with PHP, MySQL and modern JavaScript — scalable, secure, and fast.', 'code', 1),
('Frontend Design', 'Pixel-perfect, responsive UIs using Tailwind CSS, HTML5, and vanilla JS with smooth animations.', 'layout', 2),
('Database Design', 'Efficient MySQL schema design, query optimization, and data architecture for robust applications.', 'database', 3),
('API Development', 'RESTful API design and integration — connecting your frontend to any backend service.', 'server', 4),
('E-Commerce Solutions', 'Custom online stores with cart, payment gateway integration, and admin management.', 'shopping-cart', 5),
('CMS Development', 'Custom content management systems tailored to your workflow — no bloat, just control.', 'settings', 6);

-- ============================================
-- PROJECTS
-- ============================================
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    tech_stack VARCHAR(500),
    category VARCHAR(100),
    image VARCHAR(255),
    live_url VARCHAR(500),
    github_url VARCHAR(500),
    is_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO projects (title, description, tech_stack, category, image, live_url, github_url, is_featured) VALUES
('E-Commerce Platform', 'A full-featured online store with cart, payment integration, product management, and admin dashboard.', 'PHP,MySQL,JavaScript,Tailwind CSS', 'Web App', '', '#', '#', 1),
('Portfolio CMS', 'A dynamic portfolio with a custom CMS backend — all content editable without touching code.', 'PHP,MySQL,HTML,CSS,JS', 'CMS', '', '#', '#', 1),
('Task Manager App', 'Team collaboration tool with real-time updates, role-based access, and Kanban board.', 'PHP,MySQL,JavaScript,AJAX', 'Web App', '', '#', '#', 0),
('Restaurant Website', 'Elegant restaurant site with online reservation, menu management, and gallery.', 'HTML,CSS,JavaScript,PHP', 'Website', '', '#', '#', 0);

-- ============================================
-- TESTIMONIALS
-- ============================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    client_role VARCHAR(150),
    client_country VARCHAR(100),
    client_image VARCHAR(255),
    review TEXT NOT NULL,
    rating INT DEFAULT 5,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO testimonials (client_name, client_role, client_country, review, rating) VALUES
('James Wilson', 'CEO, TechStart', 'USA', 'Hasibul delivered an outstanding e-commerce platform. His attention to detail and communication throughout the project was exceptional. Highly recommended!', 5),
('Sarah Chen', 'Marketing Director', 'Canada', 'The portfolio website DevPolok built for us exceeded all expectations. Clean code, fast delivery, and the design is absolutely stunning.', 5),
('Ahmed Al-Rashid', 'Entrepreneur', 'UAE', 'Professional, reliable, and incredibly talented. He understood our vision and brought it to life perfectly. Will definitely work with again.', 5);

-- ============================================
-- CONTACT MESSAGES
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(300),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
