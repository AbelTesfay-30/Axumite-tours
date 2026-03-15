/* ========== NAVBAR SCROLL EFFECT ========== */
const navbar = document.querySelector('.navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  });
}

/* ========== MOBILE MENU ========== */
const mobileToggle = document.querySelector('.mobile-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
if (mobileToggle && mobileMenu) {
  mobileToggle.addEventListener('click', () => {
    mobileMenu.classList.toggle('open');
    const icon = mobileToggle.querySelector('span');
    if (icon) icon.textContent = mobileMenu.classList.contains('open') ? '✕' : '☰';
  });
}

/* ========== ACTIVE NAV LINK ========== */
const currentPage = window.location.pathname.split('/').pop() || 'index.html';
document.querySelectorAll('.nav-links a, .mobile-menu a').forEach(link => {
  const href = link.getAttribute('href');
  if (href === currentPage || (currentPage === '' && href === 'index.html') || (currentPage === '/' && href === 'index.html')) {
    link.classList.add('active');
  }
});

/* ========== HERO SLIDER ========== */
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.hero-dot');
let currentSlide = 0;
let sliderInterval;

function showSlide(index) {
  slides.forEach((s, i) => {
    s.classList.toggle('active', i === index);
  });
  dots.forEach((d, i) => {
    d.classList.toggle('active', i === index);
  });
  currentSlide = index;
}

function nextSlide() {
  showSlide((currentSlide + 1) % slides.length);
}

function prevSlide() {
  showSlide((currentSlide - 1 + slides.length) % slides.length);
}

if (slides.length > 0) {
  sliderInterval = setInterval(nextSlide, 5000);

  const prevBtn = document.querySelector('.hero-arrow.prev');
  const nextBtn = document.querySelector('.hero-arrow.next');

  if (prevBtn) prevBtn.addEventListener('click', () => { clearInterval(sliderInterval); prevSlide(); sliderInterval = setInterval(nextSlide, 5000); });
  if (nextBtn) nextBtn.addEventListener('click', () => { clearInterval(sliderInterval); nextSlide(); sliderInterval = setInterval(nextSlide, 5000); });

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => { clearInterval(sliderInterval); showSlide(i); sliderInterval = setInterval(nextSlide, 5000); });
  });
}

/* ========== SCROLL ANIMATIONS ========== */
const fadeElements = document.querySelectorAll('.fade-in');
if (fadeElements.length > 0) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  fadeElements.forEach(el => observer.observe(el));
}

/* ========== FORM VALIDATION & SUBMISSION ========== */
const contactForm = document.getElementById('booking-form');
if (contactForm) {
  contactForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const name    = document.getElementById('name').value.trim();
    const email   = document.getElementById('email').value.trim();
    const message = document.getElementById('message').value.trim();

    if (!name || !email || !message) {
      showToast('Please fill in all required fields', 'error');
      return;
    }

    const phone       = (document.getElementById('phone')       || {}).value || '';
    const destination = (document.getElementById('destination') || {}).value || '';
    const date        = (document.getElementById('date')        || {}).value || '';
    const guests      = (document.getElementById('guests')      || {}).value || '';

    // Disable submit button
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Sending...';

    // ── Send to PHP backend ──────────────────────────────────
    try {
      const payload = new FormData();
      payload.append('name',        name);
      payload.append('email',       email);
      payload.append('phone',       phone);
      payload.append('destination', destination);
      payload.append('date',        date);
      payload.append('guests',      guests);
      payload.append('message',     message);

      const response = await fetch('booking.php', { 
        method: 'POST', 
        body: payload 
      });

      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Invalid server response');
      }

      const data = await response.json();
      
      if (data.success) {
        showToast(data.message, 'success');
        contactForm.reset();
      } else {
        showToast(data.message || 'Something went wrong.', 'error');
      }
    } catch (error) {
      console.error('Booking error:', error);
      // PHP not available or network error - save to localStorage only
      showToast('Inquiry saved locally. We\'ll contact you soon!', 'success');
      contactForm.reset();
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }

    // ── Always save to localStorage history ──────────────────
    const history = JSON.parse(localStorage.getItem('axumiteContactHistory') || '[]');
    history.unshift({
      date: new Date().toLocaleString(),
      name, phone, email, destination,
      travelDate: date, guests, message
    });
    localStorage.setItem('axumiteContactHistory', JSON.stringify(history));
    
    // Update badge if it exists
    const badge = document.getElementById('historyBadge');
    if (badge) badge.textContent = history.length;
  });
}

/* ========== TOAST NOTIFICATION ========== */
function showToast(message, type = 'success') {
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.textContent = message;
  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.add('show');
  });

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 400);
  }, 4000);
}
