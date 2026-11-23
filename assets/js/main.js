/**
 * Khelo India School Games (KISG) / KIYG 2026 Portal
 * Main Interactive Frontend Engine
 */

document.addEventListener('DOMContentLoaded', () => {
  initScrollProgress();
  initScrollReveal();
  initCountdown();
  initSportFilter();
  initEligibilityChecker();
  initMobileMenu();
});

/**
 * Super Sapiens Premium Scroll Progress Bar
 */
function initScrollProgress() {
  const progressBar = document.getElementById('scroll-progress-bar');
  if (!progressBar) return;

  function updateProgress() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
    progressBar.style.width = progress + '%';
  }

  window.addEventListener('scroll', updateProgress, { passive: true });
  updateProgress();
}

/**
 * Super Sapiens Premium Scroll Emergence Engine (IntersectionObserver)
 * Smoothly pops and reveals elements as the user scrolls down the page.
 */
function initScrollReveal() {
  const selector = '.reveal, .reveal-pop, .reveal-left, .reveal-right, .reveal-fade';
  const revealElements = document.querySelectorAll(selector);

  // Auto-stagger delay for elements inside a .reveal-group
  document.querySelectorAll('.reveal-group').forEach(group => {
    const children = group.querySelectorAll(selector);
    children.forEach((child, index) => {
      if (!child.style.transitionDelay && !child.className.includes('stagger-')) {
        child.style.transitionDelay = `${(index + 1) * 90}ms`;
      }
    });
  });

  if (!('IntersectionObserver' in window)) {
    // Fallback for older browsers
    revealElements.forEach(el => el.classList.add('is-visible'));
    return;
  }

  const observerOptions = {
    root: null,
    rootMargin: '0px 0px -40px 0px',
    threshold: 0.12
  };

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        obs.unobserve(entry.target);
      }
    });
  }, observerOptions);

  revealElements.forEach(el => {
    // If element is already in initial viewport on page load, reveal it with slight stagger
    const rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight && rect.bottom > 0) {
      setTimeout(() => {
        el.classList.add('is-visible');
      }, 50);
      observer.observe(el);
    } else {
      observer.observe(el);
    }
  });

  // Safety net: reveal any remaining visible elements after initial render
  setTimeout(() => {
    revealElements.forEach(el => {
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight) {
        el.classList.add('is-visible');
      }
    });
  }, 400);
}

/**
 * 1. Live Countdown Timer to KIYG 2026 Telangana (Dec 11, 2026)
 */
function initCountdown() {
  const targetDate = new Date('December 11, 2026 09:00:00').getTime();

  const daysEl = document.getElementById('count-days');
  const hoursEl = document.getElementById('count-hours');
  const minutesEl = document.getElementById('count-minutes');
  const secondsEl = document.getElementById('count-seconds');

  if (!daysEl || !hoursEl) return;

  function update() {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance < 0) {
      if (document.getElementById('countdown-wrapper')) {
        document.getElementById('countdown-wrapper').innerHTML = 
          '<div class="text-xl font-bold text-emerald-400 py-2">🔥 8th Khelo India Youth Games (KIYG 2026) is LIVE in Telangana!</div>';
      }
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    daysEl.textContent = String(days).padStart(2, '0');
    hoursEl.textContent = String(hours).padStart(2, '0');
    minutesEl.textContent = String(minutes).padStart(2, '0');
    secondsEl.textContent = String(seconds).padStart(2, '0');
  }

  update();
  setInterval(update, 1000);
}

/**
 * 2. Interactive Sports Discipline Filter
 */
function initSportFilter() {
  const filterChips = document.querySelectorAll('.filter-chip');
  const sportCards = document.querySelectorAll('.sport-item-card');

  if (!filterChips.length || !sportCards.length) return;

  filterChips.forEach(chip => {
    chip.addEventListener('click', () => {
      filterChips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');

      const category = chip.getAttribute('data-filter');

      sportCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        if (category === 'all' || cardCategory === category) {
          card.style.display = 'flex';
          card.classList.add('animate-fadeIn');
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
}

/**
 * 3. Interactive Quick Eligibility Checker
 */
function initEligibilityChecker() {
  const form = document.getElementById('eligibility-quiz-form');
  const resultBox = document.getElementById('eligibility-result');

  if (!form || !resultBox) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const ageGroup = form.elements['age_bracket'] ? form.elements['age_bracket'].value : '';
    const schoolEnrolled = form.elements['is_enrolled'] ? form.elements['is_enrolled'].value : '';
    const tourneyLevel = form.elements['comp_level'] ? form.elements['comp_level'].value : '';

    if (!ageGroup || !schoolEnrolled || !tourneyLevel) {
      alert('Please select an option for all 3 questions.');
      return;
    }

    resultBox.classList.remove('hidden');

    if (schoolEnrolled === 'yes' && (ageGroup === 'u17' || ageGroup === 'u21')) {
      resultBox.innerHTML = `
        <div class="p-6 rounded-2xl bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent border border-emerald-500/30">
          <div class="flex items-center gap-3 mb-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-white font-bold">✓</span>
            <h4 class="text-xl font-bold text-emerald-800">You are 100% Eligible for Athlete Onboarding!</h4>
          </div>
          <p class="text-emerald-950 text-sm mb-4">
            Based on your enrollment and participation level, you qualify for official registration in the 
            <strong>${ageGroup === 'u17' ? 'Under-17 School Division' : 'Under-21 Youth Division'}</strong>. 
            Top scouts from the National Talent Identification Committee scout candidates directly from your division for the 
            <strong>official Government of India ₹5 Lakh annual scholarship (8 years, ₹40L total)</strong> and induction into Khelo India State Centres of Excellence (KISCE).
          </p>
          <a href="register.php?category=${ageGroup}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold rounded-xl shadow-lg shadow-orange-500/25 hover:from-orange-600 hover:to-amber-600 transition">
            Proceed to Onboarding Form →
          </a>
        </div>
      `;
    } else {
      resultBox.innerHTML = `
        <div class="p-6 rounded-2xl bg-amber-500/10 border border-amber-500/30">
          <div class="flex items-center gap-3 mb-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white font-bold">!</span>
            <h4 class="text-xl font-bold text-amber-900">Affiliation Review Required</h4>
          </div>
          <p class="text-amber-800 text-sm mb-4">
            Under Khelo India norms, candidates must be officially enrolled in a recognized school or college institution, 
            or nominated via National Sports Federation (NSF) quotas or wildcards. You may still apply under the open feeder selection.
          </p>
          <a href="register.php" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">
            Explore Open Feeder Registration →
          </a>
        </div>
      `;
    }

    resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
}

/**
 * 4. Mobile Menu Toggle
 */
function initMobileMenu() {
  const btn = document.getElementById('mobile-menu-btn');
  const menu = document.getElementById('mobile-menu');

  if (!btn || !menu) return;

  btn.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });
}

