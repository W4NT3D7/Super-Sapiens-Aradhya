/**
 * Khelo India School Games (KISG) / KIYG 2026 Portal
 * Multi-Step Athlete Onboarding Wizard Engine
 */

document.addEventListener('DOMContentLoaded', () => {
  let currentStep = 1;
  const totalSteps = 4;

  const steps = [
    document.getElementById('step-1'),
    document.getElementById('step-2'),
    document.getElementById('step-3'),
    document.getElementById('step-4')
  ];

  const wizardIndicators = [
    document.getElementById('wizard-node-1'),
    document.getElementById('wizard-node-2'),
    document.getElementById('wizard-node-3'),
    document.getElementById('wizard-node-4')
  ];

  const progressBar = document.getElementById('wizard-progress-bar');
  const form = document.getElementById('athlete-registration-form');
  const btnNext1 = document.getElementById('btn-next-1');
  const btnNext2 = document.getElementById('btn-next-2');
  const btnNext3 = document.getElementById('btn-next-3');
  const btnPrev2 = document.getElementById('btn-prev-2');
  const btnPrev3 = document.getElementById('btn-prev-3');
  const btnPrev4 = document.getElementById('btn-prev-4');
  const submitBtn = document.getElementById('btn-submit-registration');
  const sportSelect = document.getElementById('sport_discipline');
  const subEventInput = document.getElementById('sub_event');
  const subEventSuggestions = document.getElementById('sub-event-suggestions');

  // Sport to Sub-Events Mapping for smart recommendations
  const sportSubEvents = {
    'Athletics': ['100m Sprint', '200m Sprint', '400m', '800m', '1500m', 'Long Jump', 'High Jump', 'Shot Put', 'Javelin Throw', 'Relay 4x100m'],
    'Archery': ['Recurve 70m', 'Compound 50m', 'Indian Round (Traditional Bamboo Bow)'],
    'Badminton': ['Men\'s Singles', 'Women\'s Singles', 'Men\'s Doubles', 'Women\'s Doubles', 'Mixed Doubles'],
    'Boxing': ['Light Flyweight', 'Flyweight', 'Bantamweight', 'Featherweight', 'Lightweight', 'Welterweight', 'Middleweight'],
    'Swimming': ['50m Freestyle', '100m Freestyle', '100m Backstroke', '100m Breaststroke', '100m Butterfly', '200m Individual Medley'],
    'Gymnastics': ['Artistic Gymnastics (Vault/Bars/Beam)', 'Rhythmic Gymnastics', 'Trampoline'],
    'Wrestling': ['Freestyle 57kg', 'Freestyle 65kg', 'Freestyle 74kg', 'Greco-Roman 60kg', 'Greco-Roman 67kg'],
    'Weightlifting': ['49kg Category', '55kg Category', '61kg Category', '67kg Category', '73kg Category', '81kg Category'],
    'Shooting': ['10m Air Rifle', '10m Air Pistol', '50m Rifle 3 Positions', '25m Rapid Fire Pistol', 'Trap', 'Skeet'],
    'Mallakhamba': ['Pole Mallakhamba', 'Rope Mallakhamba', 'Hanging Mallakhamba'],
    'Kalaripayattu': ['Chuvadukal (Basic Stances)', 'Meypayattu (Body Conditioning)', 'Kolthari (Stick Fight)', 'Angathari (Metal Weapons)', 'Verumkai (Unarmed Combat)'],
    'Gatka': ['Single Soti (Individual Fight)', 'Farri-Soti (Team Combat)', 'Demo Weaponry Art'],
    'Thang-Ta': ['Thang-Chungoi (Sword & Shield)', 'Ta-Khouba (Spear Techniques)', 'Free Combat Sparring'],
    'Yogasana': ['Traditional Yogasana', 'Artistic Single Yogasana', 'Artistic Pair Yogasana', 'Rhythmic Yogasana'],
    'Softball (Competitive Debut)': ['Pitcher', 'Catcher', 'Infielder', 'Outfielder', 'Designated Hitter'],
    'Karra Samu (Demonstration Traditional Martial Art)': ['Single Stick Martial Sequence', 'Dual Stick Combat Form', 'Traditional Silambam/Stick Defense'],
    'Sailing (Demonstration)': ['Laser 4.7', 'Optimist Class', 'Windsurfer Class']
  };

  // Populate dynamic sub-event suggestions when sport changes
  if (sportSelect) {
    sportSelect.addEventListener('change', () => {
      const selected = sportSelect.value;
      if (subEventSuggestions && sportSubEvents[selected]) {
        subEventSuggestions.innerHTML = '';
        sportSubEvents[selected].forEach(sub => {
          const pill = document.createElement('button');
          pill.type = 'button';
          pill.className = 'text-xs font-semibold px-2.5 py-1 rounded-md bg-orange-100 text-orange-800 hover:bg-orange-200 transition';
          pill.textContent = sub;
          pill.addEventListener('click', () => {
            subEventInput.value = sub;
          });
          subEventSuggestions.appendChild(pill);
        });
        subEventSuggestions.parentElement.classList.remove('hidden');
      } else if (subEventSuggestions) {
        subEventSuggestions.parentElement.classList.add('hidden');
      }
    });
  }

  // Step Update Function
  function goToStep(step) {
    steps.forEach((s, idx) => {
      if (s) {
        if (idx + 1 === step) {
          s.classList.remove('hidden');
        } else {
          s.classList.add('hidden');
        }
      }
    });

    wizardIndicators.forEach((node, idx) => {
      if (node) {
        const stepParent = node.closest('.wizard-step');
        if (idx + 1 === step) {
          stepParent.classList.add('active');
          stepParent.classList.remove('completed');
        } else if (idx + 1 < step) {
          stepParent.classList.remove('active');
          stepParent.classList.add('completed');
        } else {
          stepParent.classList.remove('active');
          stepParent.classList.remove('completed');
        }
      }
    });

    if (progressBar) {
      const progressPercent = ((step - 1) / (totalSteps - 1)) * 100;
      progressBar.style.width = `${progressPercent}%`;
    }

    currentStep = step;
    window.scrollTo({ top: 150, behavior: 'smooth' });

    if (step === 4) {
      populateReviewCard();
    }
  }

  // Step 1 Validation
  function validateStep1() {
    const fullName = document.getElementById('full_name')?.value.trim();
    const dob = document.getElementById('dob')?.value;
    const gender = document.getElementById('gender')?.value;
    const guardianName = document.getElementById('guardian_name')?.value.trim();
    const phone = document.getElementById('phone')?.value.trim();
    const email = document.getElementById('email')?.value.trim();
    const aadhaar = document.getElementById('aadhaar_no')?.value.trim();
    const state = document.getElementById('state')?.value;
    const district = document.getElementById('district')?.value.trim();
    const schoolName = document.getElementById('school_name')?.value.trim();
    const board = document.getElementById('board')?.value;

    if (!fullName || !dob || !gender || !guardianName || !phone || !email || !aadhaar || !state || !district || !schoolName || !board) {
      alert('Please fill out all mandatory fields in Step 1.');
      return false;
    }

    if (!/^\d{10}$/.test(phone)) {
      alert('Please enter a valid 10-digit mobile number.');
      return false;
    }

    if (!/^\d{12}$/.test(aadhaar.replace(/\s+/g, ''))) {
      alert('Please enter a valid 12-digit Aadhaar / Identification number.');
      return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      alert('Please enter a valid email address.');
      return false;
    }

    return true;
  }

  // Step 2 Validation
  function validateStep2() {
    const ageCategory = document.getElementById('age_category')?.value;
    const sport = document.getElementById('sport_discipline')?.value;
    const subEvent = document.getElementById('sub_event')?.value.trim();

    if (!ageCategory || !sport || !subEvent) {
      alert('Please select your Age Division, Sports Discipline, and Sub-event / Weight class.');
      return false;
    }

    return true;
  }

  // Step 3 Validation
  function validateStep3() {
    const qualLevel = document.getElementById('qualification_level')?.value;
    const achievements = document.getElementById('achievement_details')?.value.trim();

    if (!qualLevel) {
      alert('Please specify your qualifying competition level.');
      return false;
    }

    if (!achievements) {
      alert('Please briefly summarize your key tournaments and medal record.');
      return false;
    }

    return true;
  }

  // Populate Step 4 Review
  function populateReviewCard() {
    const map = {
      'rev-name': document.getElementById('full_name')?.value,
      'rev-dob': document.getElementById('dob')?.value,
      'rev-gender': document.getElementById('gender')?.value,
      'rev-contact': (document.getElementById('phone')?.value || '') + ' | ' + (document.getElementById('email')?.value || ''),
      'rev-school': (document.getElementById('school_name')?.value || '') + ' (' + (document.getElementById('board')?.value || '') + ')',
      'rev-location': (document.getElementById('district')?.value || '') + ', ' + (document.getElementById('state')?.value || ''),
      'rev-sport': document.getElementById('sport_discipline')?.value,
      'rev-event': document.getElementById('sub_event')?.value,
      'rev-age-cat': document.getElementById('age_category')?.value,
      'rev-qual': document.getElementById('qualification_level')?.value,
      'rev-achieve': document.getElementById('achievement_details')?.value,
      'rev-coach': (document.getElementById('coach_name')?.value || 'Self/School Coach') + ' (' + (document.getElementById('coach_phone')?.value || 'N/A') + ')'
    };

    for (const [id, val] of Object.entries(map)) {
      const el = document.getElementById(id);
      if (el) el.textContent = val || 'N/A';
    }
  }

  // Navigation Button Handlers
  if (btnNext1) {
    btnNext1.addEventListener('click', () => {
      if (validateStep1()) goToStep(2);
    });
  }

  if (btnNext2) {
    btnNext2.addEventListener('click', () => {
      if (validateStep2()) goToStep(3);
    });
  }

  if (btnNext3) {
    btnNext3.addEventListener('click', () => {
      if (validateStep3()) goToStep(4);
    });
  }

  if (btnPrev2) btnPrev2.addEventListener('click', () => goToStep(1));
  if (btnPrev3) btnPrev3.addEventListener('click', () => goToStep(2));
  if (btnPrev4) btnPrev4.addEventListener('click', () => goToStep(3));

  // Form Submission via AJAX (Supports 5k concurrent users with double-submission prevention)
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const declaration = document.getElementById('declaration_check');
      if (declaration && !declaration.checked) {
        alert('Please acknowledge the declaration and code of sports ethics.');
        return;
      }

      // Disable button and show spinner
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Submitting to National Repository...
        `;
      }

      const formData = new FormData(form);

      try {
        const response = await fetch('submit_registration.php', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (result.status === 'success') {
          // Redirect immediately to confirmation digital pass
          window.location.href = `success.php?app_id=${encodeURIComponent(result.application_id)}`;
        } else {
          alert('Registration Notice: ' + (result.message || 'An error occurred during submission.'));
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Complete Official Registration →';
          }
        }
      } catch (err) {
        console.error('Submission error:', err);
        // Fallback standard submit if AJAX fetch fails
        form.submit();
      }
    });
  }
});

