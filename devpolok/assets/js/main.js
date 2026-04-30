// ============================================
// DevPolok Portfolio CMS - Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', () => {

  // ===== CUSTOM CURSOR =====
  const dot = document.getElementById('cursorDot');
  const outline = document.getElementById('cursorOutline');
  if (dot && outline) {
    let mouseX = 0, mouseY = 0;
    let outlineX = 0, outlineY = 0;
    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      dot.style.left = mouseX + 'px';
      dot.style.top = mouseY + 'px';
    });
    function animateCursor() {
      outlineX += (mouseX - outlineX) * 0.15;
      outlineY += (mouseY - outlineY) * 0.15;
      outline.style.left = outlineX + 'px';
      outline.style.top = outlineY + 'px';
      requestAnimationFrame(animateCursor);
    }
    animateCursor();
    document.querySelectorAll('a, button, .skill-tab, .filter-btn').forEach(el => {
      el.addEventListener('mouseenter', () => {
        outline.style.width = '60px';
        outline.style.height = '60px';
        outline.style.borderColor = 'rgba(99,102,241,0.8)';
      });
      el.addEventListener('mouseleave', () => {
        outline.style.width = '36px';
        outline.style.height = '36px';
        outline.style.borderColor = 'rgba(99,102,241,0.5)';
      });
    });
  }

  // ===== DARK/LIGHT THEME TOGGLE =====
  const themeToggle = document.getElementById('themeToggle');
  const html = document.documentElement;
  const savedTheme = localStorage.getItem('devpolok_theme') || html.getAttribute('data-theme') || 'dark';
  html.setAttribute('data-theme', savedTheme);

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const current = html.getAttribute('data-theme');
      const newTheme = current === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-theme', newTheme);
      localStorage.setItem('devpolok_theme', newTheme);
    });
  }

  // ===== NAVBAR SCROLL =====
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // ===== ACTIVE NAV LINK ON SCROLL =====
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');
  const observerNav = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + entry.target.id) {
            link.classList.add('active');
          }
        });
      }
    });
  }, { threshold: 0.4 });
  sections.forEach(section => observerNav.observe(section));

  // ===== MOBILE MENU =====
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('active');
      mobileMenu.classList.toggle('open');
    });
    document.querySelectorAll('.mobile-link').forEach(link => {
      link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        mobileMenu.classList.remove('open');
      });
    });
  }

  // ===== TYPING EFFECT =====
  const typingEl = document.getElementById('typingText');
  if (typingEl && typeof TYPING_WORDS !== 'undefined') {
    let wordIndex = 0;
    let charIndex = 0;
    let isDeleting = false;

    function type() {
      const word = TYPING_WORDS[wordIndex % TYPING_WORDS.length];
      if (isDeleting) {
        typingEl.textContent = word.substring(0, charIndex - 1);
        charIndex--;
      } else {
        typingEl.textContent = word.substring(0, charIndex + 1);
        charIndex++;
      }

      let delay = isDeleting ? 60 : 100;

      if (!isDeleting && charIndex === word.length) {
        delay = 2000;
        isDeleting = true;
      } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        wordIndex++;
        delay = 400;
      }

      setTimeout(type, delay);
    }
    setTimeout(type, 500);
  }

  // ===== SCROLL REVEAL =====
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        entry.target.style.transitionDelay = (i * 0.08) + 's';
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

  document.querySelectorAll('.reveal, .reveal-right').forEach(el => {
    revealObserver.observe(el);
  });

  // ===== SKILL BARS ANIMATION =====
  const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const bar = entry.target.querySelector('.skill-progress');
        if (bar) {
          const width = bar.getAttribute('data-width');
          setTimeout(() => { bar.style.width = width + '%'; }, 200);
        }
        skillObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });
  document.querySelectorAll('.skill-card').forEach(card => skillObserver.observe(card));

  // ===== SKILL TABS =====
  const skillTabs = document.querySelectorAll('.skill-tab');
  const skillCards = document.querySelectorAll('.skill-card');
  skillTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      skillTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const cat = tab.getAttribute('data-category');
      skillCards.forEach(card => {
        if (cat === 'all' || card.getAttribute('data-category') === cat) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // ===== PROJECT FILTER =====
  const filterBtns = document.querySelectorAll('.filter-btn');
  const projectCards = document.querySelectorAll('.project-card');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.getAttribute('data-filter');
      projectCards.forEach(card => {
        if (filter === 'all' || card.getAttribute('data-category') === filter) {
          card.style.display = 'block';
          card.style.animation = 'fadeInUp 0.4s ease both';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // ===== TESTIMONIAL SLIDER =====
  const track = document.getElementById('testimonialsTrack');
  if (track) {
    const cards = track.querySelectorAll('.testimonial-card');
    const dotsContainer = document.getElementById('testiDots');
    let current = 0;

    // Create dots
    cards.forEach((_, i) => {
      const dot = document.createElement('div');
      dot.className = 'testi-dot' + (i === 0 ? ' active' : '');
      dot.addEventListener('click', () => goTo(i));
      dotsContainer.appendChild(dot);
    });

    function goTo(index) {
      cards[current].classList.remove('active');
      document.querySelectorAll('.testi-dot')[current].classList.remove('active');
      current = (index + cards.length) % cards.length;
      cards[current].classList.add('active');
      document.querySelectorAll('.testi-dot')[current].classList.add('active');
    }

    if (cards.length > 0) cards[0].classList.add('active');
    document.getElementById('testiPrev')?.addEventListener('click', () => goTo(current - 1));
    document.getElementById('testiNext')?.addEventListener('click', () => goTo(current + 1));

    // Auto-slide
    setInterval(() => goTo(current + 1), 5000);
  }

  // ===== CONTACT FORM =====
  const form = document.getElementById('contactForm');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = form.querySelector('.submit-btn');
      const btnText = btn.querySelector('.btn-text');
      const btnLoading = btn.querySelector('.btn-loading');
      const msgEl = document.getElementById('formMsg');

      btnText.classList.add('hidden');
      btnLoading.classList.remove('hidden');
      btn.disabled = true;

      const data = new FormData(form);
      data.append('action', 'contact');

      try {
        const response = await fetch('api.php', { method: 'POST', body: data });
        const result = await response.json();

        msgEl.classList.remove('hidden', 'success', 'error');
        if (result.success) {
          msgEl.classList.add('success');
          msgEl.textContent = result.message || 'Message sent successfully! I\'ll get back to you soon.';
          form.reset();
        } else {
          msgEl.classList.add('error');
          msgEl.textContent = result.message || 'Something went wrong. Please try again.';
        }
      } catch (err) {
        msgEl.classList.remove('hidden');
        msgEl.classList.add('error');
        msgEl.textContent = 'Network error. Please try again.';
      } finally {
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
        btn.disabled = false;
        setTimeout(() => msgEl.classList.add('hidden'), 6000);
      }
    });
  }

  // ===== SMOOTH ANCHOR SCROLL =====
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const target = document.querySelector(link.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset = 80;
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    });
  });

});
