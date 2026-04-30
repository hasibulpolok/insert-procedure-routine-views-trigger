<?php
require_once 'config.php';
$settings = getAllSettings();
$pdo = getDB();

// Fetch nav menu
$navItems = $pdo->query("SELECT * FROM nav_menu WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Fetch skills
$skills = $pdo->query("SELECT * FROM skills WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
$skillCategories = array_unique(array_column($skills, 'category'));

// Fetch services
$services = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Fetch projects
$projects = $pdo->query("SELECT * FROM projects WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC")->fetchAll();
$projectCategories = array_unique(array_column($projects, 'category'));

// Fetch testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

$typingWords = explode(',', $settings['typing_words'] ?? 'Full Stack Developer');
$typingWordsJson = json_encode(array_map('trim', $typingWords));
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= sanitize($settings['dark_mode_default'] ?? 'dark') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitize($settings['site_description'] ?? '') ?>">
    <meta name="keywords" content="<?= sanitize($settings['site_keywords'] ?? '') ?>">
    <meta property="og:title" content="<?= sanitize($settings['site_title'] ?? 'DevPolok') ?>">
    <meta property="og:description" content="<?= sanitize($settings['site_description'] ?? '') ?>">
    <title><?= sanitize($settings['site_title'] ?? 'DevPolok | Full Stack Developer') ?></title>
    <?php if (!empty($settings['site_favicon'])): ?>
    <link rel="icon" href="<?= UPLOAD_URL . sanitize($settings['site_favicon']) ?>">
    <?php else: ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💻</text></svg>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="transition-colors duration-300">

<!-- CURSOR -->
<div class="cursor-dot" id="cursorDot"></div>
<div class="cursor-outline" id="cursorOutline"></div>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="#home" class="nav-logo">
            <?php if (!empty($settings['site_logo'])): ?>
                <img src="<?= UPLOAD_URL . sanitize($settings['site_logo']) ?>" alt="Logo" class="logo-img">
            <?php else: ?>
                <span class="logo-text">Dev<span class="accent">Polok</span></span>
            <?php endif; ?>
        </a>
        <ul class="nav-links" id="navLinks">
            <?php foreach ($navItems as $item): ?>
            <li><a href="<?= sanitize($item['url']) ?>" class="nav-link"><?= sanitize($item['title']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="nav-actions">
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                <i class="fas fa-sun sun-icon"></i>
                <i class="fas fa-moon moon-icon"></i>
            </button>
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
    <ul>
        <?php foreach ($navItems as $item): ?>
        <li><a href="<?= sanitize($item['url']) ?>" class="mobile-link"><?= sanitize($item['title']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- ===== HERO SECTION ===== -->
<section id="home" class="hero-section">
    <div class="hero-bg">
        <div class="grid-lines"></div>
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    <div class="hero-container">
        <div class="hero-content reveal">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                Available for hire
            </div>
            <h1 class="hero-title"><?= $settings['hero_title'] ?? 'Hi, I\'m <span class="highlight">Hasibul Polok</span>' ?></h1>
            <div class="hero-role">
                <span>I'm a </span>
                <span class="typing-text" id="typingText"></span>
                <span class="typing-cursor">|</span>
            </div>
            <p class="hero-desc"><?= sanitize($settings['hero_description'] ?? '') ?></p>
            <div class="hero-cta">
                <a href="#projects" class="btn-primary">
                    <?= sanitize($settings['hero_cta_primary'] ?? 'View My Work') ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="#contact" class="btn-secondary">
                    <?= sanitize($settings['hero_cta_secondary'] ?? 'Hire Me') ?>
                </a>
            </div>
            <div class="hero-socials">
                <?php if (!empty($settings['social_github'])): ?>
                <a href="<?= sanitize($settings['social_github']) ?>" target="_blank" class="social-link"><i class="fab fa-github"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['social_linkedin'])): ?>
                <a href="<?= sanitize($settings['social_linkedin']) ?>" target="_blank" class="social-link"><i class="fab fa-linkedin"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['social_instagram'])): ?>
                <a href="<?= sanitize($settings['social_instagram']) ?>" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['social_fiverr'])): ?>
                <a href="<?= sanitize($settings['social_fiverr']) ?>" target="_blank" class="social-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16.25 16.25v-10h-10v10h10zm0-12.5A2.25 2.25 0 0118.5 6v10a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 16V6A2.25 2.25 0 016 3.75h10.25z"/><path d="M8 8h2v2H8zm0 4h2v2H8zm4-4h2v2h-2zm0 4h2v2h-2z"/></svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-visual reveal-right">
            <div class="code-card">
                <div class="code-header">
                    <div class="code-dots">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                    </div>
                    <span class="code-title">devpolok.php</span>
                </div>
                <pre class="code-content"><code><span class="kw">class</span> <span class="cn">DevPolok</span> {
  <span class="kw">public</span> <span class="var">$name</span> = <span class="str">"Hasibul Polok"</span>;
  <span class="kw">public</span> <span class="var">$role</span> = <span class="str">"Full Stack Dev"</span>;
  <span class="kw">public</span> <span class="var">$stack</span> = [
    <span class="str">"PHP"</span>, <span class="str">"MySQL"</span>,
    <span class="str">"JavaScript"</span>,
    <span class="str">"Tailwind CSS"</span>
  ];

  <span class="kw">public function</span> <span class="fn">build</span>() {
    <span class="kw">return</span> <span class="str">"Amazing Products 🚀"</span>;
  }
}</code></pre>
            </div>
            <div class="floating-badges">
                <div class="float-badge fb-1"><i class="fab fa-php"></i> PHP</div>
                <div class="float-badge fb-2"><i class="fas fa-database"></i> MySQL</div>
                <div class="float-badge fb-3"><i class="fab fa-js"></i> JS</div>
                <div class="float-badge fb-4">⭐ 5.0 Rated</div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <div class="scroll-mouse"><div class="scroll-wheel"></div></div>
        <span>Scroll Down</span>
    </div>
</section>

<!-- ===== STATS BAR ===== -->
<section class="stats-bar">
    <div class="stats-container">
        <div class="stat-item reveal">
            <span class="stat-num" data-target="<?= sanitize($settings['about_experience'] ?? '3+') ?>"><?= sanitize($settings['about_experience'] ?? '3+') ?></span>
            <span class="stat-label">Years Experience</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item reveal">
            <span class="stat-num" data-target="<?= sanitize($settings['about_projects'] ?? '50+') ?>"><?= sanitize($settings['about_projects'] ?? '50+') ?></span>
            <span class="stat-label">Projects Done</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item reveal">
            <span class="stat-num" data-target="<?= sanitize($settings['about_clients'] ?? '30+') ?>"><?= sanitize($settings['about_clients'] ?? '30+') ?></span>
            <span class="stat-label">Happy Clients</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item reveal">
            <span class="stat-num">5.0 ⭐</span>
            <span class="stat-label">Average Rating</span>
        </div>
    </div>
</section>

<!-- ===== ABOUT SECTION ===== -->
<section id="about" class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-image-wrap reveal">
                <div class="about-img-container">
                    <?php if (!empty($settings['about_image'])): ?>
                    <img src="<?= UPLOAD_URL . sanitize($settings['about_image']) ?>" alt="Hasibul Polok" class="about-img">
                    <?php else: ?>
                    <div class="about-img-placeholder">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <?php endif; ?>
                    <div class="img-decoration"></div>
                </div>
                <div class="experience-badge">
                    <span class="exp-num"><?= sanitize($settings['about_experience'] ?? '3+') ?></span>
                    <span class="exp-text">Years of<br>Experience</span>
                </div>
            </div>
            <div class="about-content reveal-right">
                <div class="section-tag">Get to Know Me</div>
                <h2 class="section-title"><?= sanitize($settings['about_title'] ?? 'About Me') ?></h2>
                <div class="about-bio"><?= nl2br(sanitize($settings['about_bio'] ?? '')) ?></div>
                <div class="about-tags">
                    <span class="tag"><i class="fas fa-map-marker-alt"></i> Bangladesh</span>
                    <span class="tag"><i class="fas fa-envelope"></i> <?= sanitize($settings['contact_email'] ?? '') ?></span>
                    <span class="tag"><i class="fab fa-whatsapp"></i> Available</span>
                </div>
                <div class="about-actions">
                    <a href="<?= sanitize($settings['social_github'] ?? '#') ?>" target="_blank" class="btn-primary">
                        <i class="fab fa-github"></i> GitHub Profile
                    </a>
                    <a href="<?= sanitize($settings['social_fiverr'] ?? '#') ?>" target="_blank" class="btn-outline">
                        Hire on Fiverr
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SKILLS SECTION ===== -->
<section id="skills" class="section section-alt">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">Expertise</div>
            <h2 class="section-title"><?= sanitize($settings['skills_title'] ?? 'My Tech Stack') ?></h2>
        </div>
        <div class="skills-tabs reveal">
            <button class="skill-tab active" data-category="all">All</button>
            <?php foreach ($skillCategories as $cat): ?>
            <button class="skill-tab" data-category="<?= sanitize($cat) ?>"><?= sanitize($cat) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="skills-grid" id="skillsGrid">
            <?php foreach ($skills as $skill): ?>
            <div class="skill-card reveal" data-category="<?= sanitize($skill['category']) ?>">
                <div class="skill-header">
                    <div class="skill-icon">
                        <?php
                        $icons = [
                            'html5' => '<i class="fab fa-html5" style="color:#e34f26"></i>',
                            'css3' => '<i class="fab fa-css3-alt" style="color:#1572b6"></i>',
                            'javascript' => '<i class="fab fa-js-square" style="color:#f7df1e"></i>',
                            'php' => '<i class="fab fa-php" style="color:#777bb4"></i>',
                            'mysql' => '<i class="fas fa-database" style="color:#00758f"></i>',
                            'git' => '<i class="fab fa-git-alt" style="color:#f34f29"></i>',
                            'bootstrap' => '<i class="fab fa-bootstrap" style="color:#7952b3"></i>',
                            'tailwind' => '<i class="fas fa-wind" style="color:#38bdf8"></i>',
                        ];
                        echo $icons[strtolower($skill['icon'])] ?? '<i class="fas fa-code"></i>';
                        ?>
                    </div>
                    <div class="skill-info">
                        <span class="skill-name"><?= sanitize($skill['name']) ?></span>
                        <span class="skill-pct"><?= (int)$skill['percentage'] ?>%</span>
                    </div>
                </div>
                <div class="skill-bar">
                    <div class="skill-progress" data-width="<?= (int)$skill['percentage'] ?>" style="width: 0%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== SERVICES SECTION ===== -->
<section id="services" class="section">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">What I Do</div>
            <h2 class="section-title"><?= sanitize($settings['services_title'] ?? 'What I Offer') ?></h2>
        </div>
        <div class="services-grid">
            <?php foreach ($services as $i => $service): ?>
            <div class="service-card reveal" style="--delay: <?= $i * 0.1 ?>s">
                <div class="service-icon-wrap">
                    <?php
                    $serviceIcons = [
                        'code' => 'fa-code',
                        'layout' => 'fa-desktop',
                        'database' => 'fa-database',
                        'server' => 'fa-server',
                        'shopping-cart' => 'fa-shopping-cart',
                        'settings' => 'fa-cogs',
                    ];
                    $iconClass = $serviceIcons[$service['icon']] ?? 'fa-star';
                    ?>
                    <i class="fas <?= $iconClass ?> service-icon"></i>
                </div>
                <h3 class="service-title"><?= sanitize($service['title']) ?></h3>
                <p class="service-desc"><?= sanitize($service['description']) ?></p>
                <div class="service-number"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== PROJECTS SECTION ===== -->
<section id="projects" class="section section-alt">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">Portfolio</div>
            <h2 class="section-title"><?= sanitize($settings['projects_title'] ?? 'Featured Projects') ?></h2>
        </div>
        <div class="project-filters reveal">
            <button class="filter-btn active" data-filter="all">All Projects</button>
            <?php foreach ($projectCategories as $cat): ?>
            <button class="filter-btn" data-filter="<?= sanitize($cat) ?>"><?= sanitize($cat) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="projects-grid" id="projectsGrid">
            <?php foreach ($projects as $project): ?>
            <div class="project-card reveal" data-category="<?= sanitize($project['category']) ?>">
                <div class="project-img-wrap">
                    <?php if (!empty($project['image'])): ?>
                    <img src="<?= UPLOAD_URL . sanitize($project['image']) ?>" alt="<?= sanitize($project['title']) ?>" class="project-img">
                    <?php else: ?>
                    <div class="project-img-placeholder">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <?php endif; ?>
                    <div class="project-overlay">
                        <div class="project-links">
                            <?php if (!empty($project['live_url'])): ?>
                            <a href="<?= sanitize($project['live_url']) ?>" target="_blank" class="proj-link-btn"><i class="fas fa-external-link-alt"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($project['github_url'])): ?>
                            <a href="<?= sanitize($project['github_url']) ?>" target="_blank" class="proj-link-btn"><i class="fab fa-github"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($project['is_featured']): ?>
                    <div class="project-featured-badge">Featured</div>
                    <?php endif; ?>
                </div>
                <div class="project-info">
                    <div class="project-category-label"><?= sanitize($project['category']) ?></div>
                    <h3 class="project-title"><?= sanitize($project['title']) ?></h3>
                    <p class="project-desc"><?= sanitize($project['description']) ?></p>
                    <div class="project-stack">
                        <?php foreach (explode(',', $project['tech_stack']) as $tech): ?>
                        <span class="stack-tag"><?= sanitize(trim($tech)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<?php if (!empty($testimonials)): ?>
<section id="testimonials" class="section">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">Client Reviews</div>
            <h2 class="section-title">What Clients Say</h2>
        </div>
        <div class="testimonials-slider reveal">
            <div class="testimonials-track" id="testimonialsTrack">
                <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card">
                    <div class="testi-stars">
                        <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>★<?php endfor; ?>
                    </div>
                    <p class="testi-review">"<?= sanitize($t['review']) ?>"</p>
                    <div class="testi-author">
                        <?php if (!empty($t['client_image'])): ?>
                        <img src="<?= UPLOAD_URL . sanitize($t['client_image']) ?>" alt="<?= sanitize($t['client_name']) ?>" class="testi-avatar">
                        <?php else: ?>
                        <div class="testi-avatar-placeholder"><?= strtoupper(substr($t['client_name'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <div class="testi-name"><?= sanitize($t['client_name']) ?></div>
                            <div class="testi-role"><?= sanitize($t['client_role']) ?>, <?= sanitize($t['client_country']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="testi-controls">
                <button class="testi-btn" id="testiPrev"><i class="fas fa-chevron-left"></i></button>
                <div class="testi-dots" id="testiDots"></div>
                <button class="testi-btn" id="testiNext"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== CONTACT SECTION ===== -->
<section id="contact" class="section section-alt">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-tag">Get In Touch</div>
            <h2 class="section-title"><?= sanitize($settings['contact_title'] ?? 'Let\'s Work Together') ?></h2>
        </div>
        <div class="contact-grid">
            <div class="contact-info reveal">
                <div class="contact-card">
                    <i class="fas fa-envelope contact-icon"></i>
                    <div>
                        <div class="contact-label">Email</div>
                        <a href="mailto:<?= sanitize($settings['contact_email'] ?? '') ?>" class="contact-value"><?= sanitize($settings['contact_email'] ?? '') ?></a>
                    </div>
                </div>
                <div class="contact-card">
                    <i class="fab fa-whatsapp contact-icon"></i>
                    <div>
                        <div class="contact-label">WhatsApp</div>
                        <a href="https://wa.me/880<?= sanitize($settings['contact_whatsapp'] ?? '') ?>" target="_blank" class="contact-value">+880 <?= sanitize($settings['contact_whatsapp'] ?? '') ?></a>
                    </div>
                </div>
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt contact-icon"></i>
                    <div>
                        <div class="contact-label">Location</div>
                        <span class="contact-value"><?= sanitize($settings['contact_location'] ?? 'Bangladesh') ?></span>
                    </div>
                </div>
                <a href="https://wa.me/880<?= sanitize($settings['contact_whatsapp'] ?? '') ?>" target="_blank" class="whatsapp-cta">
                    <i class="fab fa-whatsapp"></i>
                    Message on WhatsApp
                </a>
            </div>
            <div class="contact-form-wrap reveal-right">
                <form id="contactForm" class="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="john@example.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" placeholder="Project Inquiry">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" rows="5" placeholder="Tell me about your project..." required></textarea>
                    </div>
                    <button type="submit" class="btn-primary submit-btn">
                        <span class="btn-text"><i class="fas fa-paper-plane"></i> Send Message</span>
                        <span class="btn-loading hidden"><i class="fas fa-spinner fa-spin"></i> Sending...</span>
                    </button>
                    <div id="formMsg" class="form-message hidden"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-top">
            <div class="footer-brand">
                <span class="logo-text">Dev<span class="accent">Polok</span></span>
                <p><?= sanitize($settings['site_tagline'] ?? '') ?></p>
            </div>
            <div class="footer-socials">
                <?php if (!empty($settings['social_github'])): ?>
                <a href="<?= sanitize($settings['social_github']) ?>" target="_blank"><i class="fab fa-github"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['social_linkedin'])): ?>
                <a href="<?= sanitize($settings['social_linkedin']) ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['social_instagram'])): ?>
                <a href="<?= sanitize($settings['social_instagram']) ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['social_fiverr'])): ?>
                <a href="<?= sanitize($settings['social_fiverr']) ?>" target="_blank"><i class="fas fa-briefcase"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?= sanitize($settings['footer_text'] ?? '© 2025 DevPolok. Crafted with ❤️ in Bangladesh.') ?></p>
            <a href="admin/login.php" class="admin-link">Admin Panel</a>
        </div>
    </div>
</footer>

<script>
const TYPING_WORDS = <?= $typingWordsJson ?>;
</script>
<script src="assets/js/main.js"></script>
</body>
</html>
