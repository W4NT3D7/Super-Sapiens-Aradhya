<?php
require_once __DIR__ . '/config.php';
$sports_grouped = get_sports_disciplines();
$states = get_indian_states();
$csrf_token = get_csrf_token();
$preselected_sport = clean_input($_GET['sport'] ?? '');
$preselected_category = clean_input($_GET['category'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Athlete Onboarding & Talent Scouting Registration | Super Sapiens</title>
  <meta name="description" content="Official athlete onboarding portal powered by Super Sapiens for Khelo India School Games & KIYG 2026. Register for official Government of India ₹5 Lakh annual scholarship & national scouting.">

  <!-- Tailwind CSS & Custom Theme -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-orange-500 selection:text-white">

  <!-- Tricolor Bar -->
  <div class="gov-strip"></div>

  <!-- Header -->
  <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
    <div class="container mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 via-amber-500 to-emerald-500 text-white font-black text-lg shadow-md shadow-orange-500/20 tracking-tighter">
          SS
        </div>
        <div>
          <span class="text-[10px] font-bold tracking-wider uppercase text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200">Super Sapiens</span>
          <h1 class="text-base font-black text-slate-900 leading-none mt-1">
            ATHLETE <span class="text-orange-500">ONBOARDING</span>
          </h1>
        </div>
      </a>

      <div class="flex items-center gap-3">
        <a href="status.php" class="text-xs font-bold text-blue-600 hover:text-blue-700 px-3 py-2 rounded-lg border border-blue-200 hover:bg-blue-50 transition">
          Track Existing Application
        </a>
        <a href="index.php" class="text-xs font-bold text-slate-500 hover:text-slate-800 px-3 py-2">
          ← Back to Overview
        </a>
      </div>
    </div>
  </header>

  <!-- Notice Bar -->
  <div class="bg-orange-50 border-b border-orange-200 py-2.5 px-4 text-xs font-medium text-orange-950">
    <div class="container mx-auto flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="flex h-2 w-2 rounded-full bg-orange-500"></span>
        <span>
          <strong>Zero Registration Cost:</strong> Registrations under the National Sports Repository System (NSRS) are 100% free of charge.
        </span>
      </div>
      <span class="hidden sm:inline font-bold text-orange-700">Official Govt Scholarship Scheme: ₹5 Lakh/Yr (8 Years)</span>
    </div>
  </div>

  <!-- Main Registration Container -->
  <main class="py-10 sm:py-16">
    <div class="container mx-auto px-4 sm:px-6 max-w-4xl">
      
      <!-- Wizard Title -->
      <div class="text-center mb-10">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-orange-100 text-orange-700 mb-2">
          Grassroots Talent Induction 2026
        </span>
        <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
          Athlete & Talent Onboarding Form
        </h2>
        <p class="text-slate-600 text-sm sm:text-base mt-2">
          Register to qualify for the 8th Khelo India Youth Games (KIYG 2026 Telangana), KISCE scouting trials, and the official Government of India ₹5 Lakh/year annual scholarship grant.
        </p>
      </div>

      <!-- Multi-Step Progress Indicator -->
      <div class="wizard-step-indicator mb-10 px-4">
        <div id="wizard-progress-bar" class="wizard-progress-bar" style="width: 0%;"></div>

        <div class="wizard-step active" id="wizard-node-1">
          <div class="step-node font-bold">1</div>
          <span class="step-title">Personal & School</span>
        </div>

        <div class="wizard-step" id="wizard-node-2">
          <div class="step-node font-bold">2</div>
          <span class="step-title">Sports Discipline</span>
        </div>

        <div class="wizard-step" id="wizard-node-3">
          <div class="step-node font-bold">3</div>
          <span class="step-title">Achievements</span>
        </div>

        <div class="wizard-step" id="wizard-node-4">
          <div class="step-node font-bold">4</div>
          <span class="step-title">Review & Submit</span>
        </div>
      </div>

      <!-- Registration Form Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        
        <form id="athlete-registration-form" method="POST" action="submit_registration.php" class="p-6 sm:p-10">
          
          <!-- CSRF Token & Honeypot (Security against spam and bots) -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <div style="display:none;" aria-hidden="true">
            <input type="text" name="portal_hp_check" tabindex="-1" autocomplete="off">
          </div>

          <!-- ==========================================
               STEP 1: PERSONAL & SCHOOL PROFILE
               ========================================== -->
          <div id="step-1" class="wizard-step-content space-y-6">
            
            <div class="border-b border-slate-100 pb-4 mb-6">
              <h3 class="text-xl font-black text-slate-900">Step 1: Athlete Identity & School Credentials</h3>
              <p class="text-xs text-slate-500 mt-1">Please provide accurate identification matching your school enrollment records or Aadhaar.</p>
            </div>

            <!-- Full Name & Date of Birth -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="full_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Athlete Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="full_name" name="full_name" required placeholder="e.g. Aarav Sharma"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>

              <div>
                <label for="dob" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Date of Birth <span class="text-red-500">*</span>
                </label>
                <input type="date" id="dob" name="dob" required
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                <span class="text-[11px] text-slate-400 mt-1 block">Ages 10–17 (School) or 18–21 (Youth) as per official guidelines</span>
              </div>
            </div>

            <!-- Gender & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Gender <span class="text-red-500">*</span>
                </label>
                <select id="gender" name="gender" required
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>

              <div>
                <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Social Category
                </label>
                <select id="category" name="category"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                  <option value="General">General</option>
                  <option value="OBC">OBC</option>
                  <option value="SC">SC</option>
                  <option value="ST">ST</option>
                  <option value="EWS">EWS</option>
                </select>
              </div>
            </div>

            <!-- Aadhaar & Guardian Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="aadhaar_no" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  12-Digit Aadhaar / Unique ID Number <span class="text-red-500">*</span>
                </label>
                <input type="text" id="aadhaar_no" name="aadhaar_no" required maxlength="12" placeholder="12-digit number"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold tracking-widest">
                <span class="text-[11px] text-slate-400 mt-1 block">Stored securely as an encrypted cryptographic hash</span>
              </div>

              <div>
                <label for="guardian_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Father's / Mother's / Guardian Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="guardian_name" name="guardian_name" required placeholder="e.g. Rajesh Sharma"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>
            </div>

            <!-- Phone & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Primary Mobile Number (for SMS & Trial Alerts) <span class="text-red-500">*</span>
                </label>
                <input type="tel" id="phone" name="phone" required maxlength="10" placeholder="10-digit mobile number"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>

              <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" required placeholder="athlete@example.com"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>
            </div>

            <!-- State & District -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="state" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Home State / UT <span class="text-red-500">*</span>
                </label>
                <select id="state" name="state" required
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                  <option value="">Select State / UT</option>
                  <?php foreach ($states as $st): ?>
                    <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label for="district" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  District <span class="text-red-500">*</span>
                </label>
                <input type="text" id="district" name="district" required placeholder="e.g. Hyderabad, Pune, Kozhikode"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>
            </div>

            <!-- School & Board Affiliation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="school_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Enrolled School / Institution Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="school_name" name="school_name" required placeholder="e.g. Hyderabad Public School, Begumpet"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>

              <div>
                <label for="board" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Affiliated Board / Federation <span class="text-red-500">*</span>
                </label>
                <select id="board" name="board" required
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                  <option value="">Select Affiliation</option>
                  <option value="CBSE">CBSE (Central Board of Secondary Education)</option>
                  <option value="SGFI">SGFI (School Games Federation of India)</option>
                  <option value="State Board">State Government Board</option>
                  <option value="ICSE">CISCE / ICSE</option>
                  <option value="National Sports Federation">National Sports Federation (NSF)</option>
                  <option value="University/College">University / College Division</option>
                </select>
              </div>
            </div>

            <!-- Next Button Step 1 -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
              <button type="button" id="btn-next-1" class="px-8 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/25 transition">
                Proceed to Sports Category (Step 2) →
              </button>
            </div>

          </div>

          <!-- ==========================================
               STEP 2: SPORTS DISCIPLINE & EVENT
               ========================================== -->
          <div id="step-2" class="wizard-step-content space-y-6 hidden">
            
            <div class="border-b border-slate-100 pb-4 mb-6">
              <h3 class="text-xl font-black text-slate-900">Step 2: Sports Discipline & Level</h3>
              <p class="text-xs text-slate-500 mt-1">Select your athletic discipline across Olympic, Indigenous, or new 2026 events.</p>
            </div>

            <!-- Age Category Division -->
            <div>
              <label for="age_category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Age Division Category <span class="text-red-500">*</span>
              </label>
              <select id="age_category" name="age_category" required
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                <option value="">Select Age Category</option>
                <option value="Under-14" <?php echo ($preselected_category === 'u14') ? 'selected' : ''; ?>>Under-14 (Feeder School Division, Ages 10–13)</option>
                <option value="Under-17" <?php echo ($preselected_category === 'u17') ? 'selected' : ''; ?>>Under-17 (Khelo India School Games Flagship, Ages 14–17)</option>
                <option value="Under-21" <?php echo ($preselected_category === 'u21') ? 'selected' : ''; ?>>Under-21 (Khelo India Youth Games Division, Ages 18–21)</option>
              </select>
            </div>

            <!-- Sport Discipline Selection -->
            <div>
              <label for="sport_discipline" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Sport Discipline <span class="text-red-500">*</span>
              </label>
              <select id="sport_discipline" name="sport_discipline" required
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                <option value="">Select Sport Discipline</option>
                <?php foreach ($sports_grouped as $group_label => $sport_items): ?>
                  <optgroup label="<?php echo htmlspecialchars($group_label); ?>">
                    <?php foreach ($sport_items as $sport): ?>
                      <option value="<?php echo htmlspecialchars($sport); ?>" <?php echo ($preselected_sport === $sport) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sport); ?>
                      </option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Sub-Event / Weight Class with dynamic pills -->
            <div>
              <label for="sub_event" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Sub-Event / Weight Class / Playing Position <span class="text-red-500">*</span>
              </label>
              <input type="text" id="sub_event" name="sub_event" required placeholder="e.g. 100m Sprint, Pole Mallakhamba, 57kg Freestyle, Pitcher"
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              
              <!-- Smart Pill Suggestions Container -->
              <div class="mt-3 hidden">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Common Events in this Discipline:</span>
                <div id="sub-event-suggestions" class="flex flex-wrap gap-1.5"></div>
              </div>
            </div>

            <!-- Experience & Stance -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="playing_experience" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Years of Formal Training / Playing Experience
                </label>
                <select id="playing_experience" name="playing_experience"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                  <option value="1">Less than 1 Year / Grassroots</option>
                  <option value="2">2 Years</option>
                  <option value="3">3–4 Years</option>
                  <option value="5">5+ Years</option>
                  <option value="7">7+ Years</option>
                </select>
              </div>

              <div>
                <label for="stance" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Primary Stance / Handedness
                </label>
                <select id="stance" name="stance"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                  <option value="Right-Handed">Right-Handed / Orthodox</option>
                  <option value="Left-Handed">Left-Handed / Southpaw</option>
                  <option value="Ambidextrous">Ambidextrous / Switch</option>
                </select>
              </div>
            </div>

            <!-- Navigation Buttons Step 2 -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
              <button type="button" id="btn-prev-2" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition">
                ← Back to Step 1
              </button>
              <button type="button" id="btn-next-2" class="px-8 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/25 transition">
                Proceed to Achievements (Step 3) →
              </button>
            </div>

          </div>

          <!-- ==========================================
               STEP 3: SPORTING ACHIEVEMENTS & QUALIFICATIONS
               ========================================== -->
          <div id="step-3" class="wizard-step-content space-y-6 hidden">
            
            <div class="border-b border-slate-100 pb-4 mb-6">
              <h3 class="text-xl font-black text-slate-900">Step 3: Sporting Qualifications & Nominations</h3>
              <p class="text-xs text-slate-500 mt-1">Highlight your previous competitions, medals, and coach details for scouting priority.</p>
            </div>

            <!-- Highest Qualifying Level -->
            <div>
              <label for="qualification_level" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Highest Qualifying Tournament Level <span class="text-red-500">*</span>
              </label>
              <select id="qualification_level" name="qualification_level" required
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                <option value="">Select Qualification Tier</option>
                <option value="SGFI National School Games">SGFI National School Games (Medalist / Finalist)</option>
                <option value="CBSE National Sports Meet">CBSE National Sports Meet</option>
                <option value="National Sports Federation Cup">National Sports Federation (NSF) Championship</option>
                <option value="State Level Medalist">State Level School / Open Championship Medalist</option>
                <option value="District / Zonal Champion">District / Zonal Level Medalist</option>
                <option value="Host State / Wildcard Quota">Telangana Host State Quota / Wildcard</option>
                <option value="Grassroots Scout Candidate">Grassroots School Talent (First-time National Applicant)</option>
              </select>
            </div>

            <!-- Key Achievements Details -->
            <div>
              <label for="achievement_details" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Summary of Medals, Best Timings & Key Records <span class="text-red-500">*</span>
              </label>
              <textarea id="achievement_details" name="achievement_details" rows="3" required placeholder="e.g. Gold medal at State School Games 2025 in 100m (Timing: 10.92s); Represented Telangana at SGFI Nationals 2024-25."
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold"></textarea>
            </div>

            <!-- Certificate / License Number -->
            <div>
              <label for="certificate_no" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Certificate / NSRS UID / Federation License No. (If available)
              </label>
              <input type="text" id="certificate_no" name="certificate_no" placeholder="e.g. SGFI-TEL-2025-089 or NSRS-XXXX"
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
            </div>

            <!-- Coach / Academy Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="coach_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Coach / Training Academy Name
                </label>
                <input type="text" id="coach_name" name="coach_name" placeholder="e.g. SAI Center / Coach N. Rao"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>

              <div>
                <label for="coach_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                  Coach Contact Number
                </label>
                <input type="tel" id="coach_phone" name="coach_phone" placeholder="10-digit mobile"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
              </div>
            </div>

            <!-- Navigation Buttons Step 3 -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
              <button type="button" id="btn-prev-3" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition">
                ← Back to Step 2
              </button>
              <button type="button" id="btn-next-3" class="px-8 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/25 transition">
                Review & Confirm (Step 4) →
              </button>
            </div>

          </div>

          <!-- ==========================================
               STEP 4: REVIEW & DECLARATION
               ========================================== -->
          <div id="step-4" class="wizard-step-content space-y-6 hidden">
            
            <div class="border-b border-slate-100 pb-4 mb-6">
              <h3 class="text-xl font-black text-slate-900">Step 4: Verify Dossier & Submit</h3>
              <p class="text-xs text-slate-500 mt-1">Carefully inspect your onboarding details before generating your Official Athlete Application Pass.</p>
            </div>

            <!-- Summary Card -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-slate-200 text-sm">
                <div>
                  <span class="text-xs text-slate-400 uppercase font-bold block">Athlete Name</span>
                  <span id="rev-name" class="font-black text-slate-900 text-base"></span>
                </div>
                <div>
                  <span class="text-xs text-slate-400 uppercase font-bold block">DOB & Gender</span>
                  <span id="rev-dob" class="font-bold text-slate-800"></span> • <span id="rev-gender" class="font-bold text-slate-800"></span>
                </div>
                <div>
                  <span class="text-xs text-slate-400 uppercase font-bold block">Contact</span>
                  <span id="rev-contact" class="font-semibold text-slate-700"></span>
                </div>
                <div>
                  <span class="text-xs text-slate-400 uppercase font-bold block">Location</span>
                  <span id="rev-location" class="font-semibold text-slate-700"></span>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-slate-200 text-sm">
                <div>
                  <span class="text-xs text-slate-400 uppercase font-bold block">Enrolled Institution</span>
                  <span id="rev-school" class="font-bold text-slate-800"></span>
                </div>
                <div>
                  <span class="text-xs text-slate-400 uppercase font-bold block">Sport & Division</span>
                  <span id="rev-sport" class="font-black text-orange-600"></span> (<span id="rev-age-cat" class="font-bold text-slate-800"></span>)
                </div>
                <div class="sm:col-span-2">
                  <span class="text-xs text-slate-400 uppercase font-bold block">Sub-Event / Category</span>
                  <span id="rev-event" class="font-bold text-slate-800"></span>
                </div>
              </div>

              <div class="text-sm">
                <span class="text-xs text-slate-400 uppercase font-bold block">Qualifying Level & Record</span>
                <span id="rev-qual" class="font-bold text-blue-700 block"></span>
                <p id="rev-achieve" class="text-xs text-slate-600 mt-1 italic"></p>
              </div>

            </div>

            <!-- Legal & Sports Ethics Declaration -->
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-950 space-y-2">
              <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" id="declaration_check" required class="mt-1 rounded text-orange-500 focus:ring-orange-500">
                <span class="leading-relaxed">
                  <strong>Declaration & Code of Conduct:</strong> I certify that all details, date of birth, and sporting records provided are accurate. 
                  I agree to abide by the official rules of the Khelo India Games, National Anti-Doping Agency (NADA) codes, and understand that selection into the 
                  official Government of India (MYAS) ₹5 Lakh/year scholarship is contingent upon document verification and performance in physical trials conducted by SAI.
                </span>
              </label>
            </div>

            <!-- Submit Button Step 4 -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
              <button type="button" id="btn-prev-4" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition">
                ← Edit Details
              </button>
              <button type="submit" id="btn-submit-registration" class="px-10 py-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-base rounded-2xl shadow-xl shadow-orange-500/30 transition transform hover:-translate-y-0.5">
                Complete Official Registration →
              </button>
            </div>

          </div>

        </form>

      </div>

    </div>
  </main>

  <!-- Scripts -->
  <script src="assets/js/register.js"></script>
</body>
</html>

