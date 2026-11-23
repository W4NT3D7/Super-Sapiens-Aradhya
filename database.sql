-- ====================================================================
-- SUPER SAPIENS - ATHLETE DISCOVERY & TALENT CONTRACT EXCHANGE
-- High-Performance Database Schema for MySQL / phpMyAdmin
-- Powering Grassroots Scouting for Khelo India Games & KIYG 2026
-- Optimized for 5,000+ Concurrent Users (InnoDB, Row Locks, Indexes)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `khelo_india` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `khelo_india`;

-- --------------------------------------------------------------------
-- 1. Table: athletes (Core athlete registration & scouting records)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `trades`;
DROP TABLE IF EXISTS `athlete_contracts`;
DROP TABLE IF EXISTS `news_feed`;
DROP TABLE IF EXISTS `wallet_transactions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `athletes`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `site_settings`;

CREATE TABLE `athletes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id` VARCHAR(32) NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `dob` DATE NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `category` ENUM('General', 'OBC', 'SC', 'ST', 'EWS') DEFAULT 'General',
  `aadhaar_hash` CHAR(64) NOT NULL,
  `guardian_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `state` VARCHAR(80) NOT NULL,
  `district` VARCHAR(80) NOT NULL,
  `school_name` VARCHAR(200) NOT NULL,
  `board` VARCHAR(50) NOT NULL,
  `age_category` ENUM('Under-14', 'Under-17', 'Under-21') NOT NULL,
  `sport_discipline` VARCHAR(80) NOT NULL,
  `sub_event` VARCHAR(120) NOT NULL,
  `playing_experience` TINYINT UNSIGNED DEFAULT 1,
  `qualification_level` VARCHAR(80) NOT NULL,
  `achievement_details` TEXT DEFAULT NULL,
  `coach_name` VARCHAR(120) DEFAULT NULL,
  `coach_phone` VARCHAR(20) DEFAULT NULL,
  `certificate_no` VARCHAR(80) DEFAULT NULL,
  `status` ENUM('Under Review', 'Verified', 'Shortlisted for Trials', 'Selected for Scholarship', 'Rejected') DEFAULT 'Under Review',
  `trial_venue` VARCHAR(180) DEFAULT NULL,
  `trial_date` DATE DEFAULT NULL,
  `talent_score` DECIMAL(4,1) DEFAULT 88.5,
  `avatar_icon` VARCHAR(10) DEFAULT '🏃',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_application_id` (`application_id`),
  UNIQUE KEY `uk_aadhaar_hash` (`aadhaar_hash`),
  KEY `idx_sport_discipline` (`sport_discipline`),
  KEY `idx_state_district` (`state`, `district`),
  KEY `idx_status` (`status`),
  KEY `idx_phone_dob` (`phone`, `dob`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------------------
-- 2. Table: users (Platform users, talent scouts, traders)
-- --------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `wallet_balance` DECIMAL(12,2) NOT NULL DEFAULT 10000.00 COMMENT 'Starting virtual demo balance of ₹10,000 credits',
  `favorite_sports` VARCHAR(255) DEFAULT 'Athletics,Wrestling,Kalaripayattu',
  `kyc_status` ENUM('Pending', 'Verified') DEFAULT 'Verified',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_email` (`email`),
  UNIQUE KEY `uk_user_username` (`username`),
  KEY `idx_user_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. Table: athlete_contracts (Futures & Options contracts linked to athletes)
-- --------------------------------------------------------------------
CREATE TABLE `athlete_contracts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `athlete_id` INT UNSIGNED NOT NULL,
  `ticker` VARCHAR(24) NOT NULL COMMENT 'e.g. AARAV-100M-DEC26',
  `contract_title` VARCHAR(120) NOT NULL,
  `current_price` DECIMAL(10,2) NOT NULL DEFAULT 100.00,
  `previous_close` DECIMAL(10,2) NOT NULL DEFAULT 95.00,
  `high_24h` DECIMAL(10,2) NOT NULL DEFAULT 112.00,
  `low_24h` DECIMAL(10,2) NOT NULL DEFAULT 91.00,
  `volume_contracts` INT UNSIGNED NOT NULL DEFAULT 2400,
  `open_interest` INT UNSIGNED NOT NULL DEFAULT 1500,
  `contract_expiry` DATE NOT NULL,
  `lot_size` SMALLINT UNSIGNED NOT NULL DEFAULT 10,
  `status` ENUM('ACTIVE', 'HALTED', 'EXPIRED') DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ticker` (`ticker`),
  KEY `idx_athlete_contract` (`athlete_id`),
  CONSTRAINT `fk_contract_athlete` FOREIGN KEY (`athlete_id`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. Table: trades (User positions in athlete contracts)
-- --------------------------------------------------------------------
CREATE TABLE `trades` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `contract_id` INT UNSIGNED NOT NULL,
  `order_type` ENUM('MARKET', 'LIMIT') DEFAULT 'MARKET',
  `direction` ENUM('BUY', 'SELL') NOT NULL COMMENT 'BUY (Long) or SELL (Short)',
  `quantity` INT UNSIGNED NOT NULL COMMENT 'Number of contracts',
  `entry_price` DECIMAL(10,2) NOT NULL,
  `current_price` DECIMAL(10,2) NOT NULL,
  `margin_used` DECIMAL(12,2) NOT NULL,
  `status` ENUM('OPEN', 'CLOSED') DEFAULT 'OPEN',
  `pnl` DECIMAL(12,2) DEFAULT 0.00,
  `closed_price` DECIMAL(10,2) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_trades_user_status` (`user_id`, `status`),
  KEY `idx_trades_contract` (`contract_id`),
  CONSTRAINT `fk_trades_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_trades_contract` FOREIGN KEY (`contract_id`) REFERENCES `athlete_contracts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. Table: wallet_transactions (Audit ledger for all wallet movements)
-- --------------------------------------------------------------------
CREATE TABLE `wallet_transactions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `type` ENUM('DEPOSIT', 'WITHDRAW', 'TRADE_MARGIN', 'TRADE_PNL', 'SIGNUP_BONUS') NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `balance_after` DECIMAL(12,2) NOT NULL,
  `txn_ref` VARCHAR(40) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_txn_user` (`user_id`),
  KEY `idx_txn_ref` (`txn_ref`),
  CONSTRAINT `fk_txn_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 6. Table: news_feed (Algorithm news updates linked to athletes)
-- --------------------------------------------------------------------
CREATE TABLE `news_feed` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `athlete_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `summary` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL COMMENT 'Trials, Medal, KISCE, Scouting, Record',
  `sentiment` ENUM('BULLISH', 'NEUTRAL', 'BEARISH') DEFAULT 'BULLISH',
  `impact_pct` DECIMAL(4,1) DEFAULT 5.0,
  `published_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_news_athlete` (`athlete_id`),
  KEY `idx_news_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 7. Table: admins & site_settings
-- --------------------------------------------------------------------
CREATE TABLE `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(30) DEFAULT 'Official',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `site_settings` (
  `setting_key` VARCHAR(60) NOT NULL,
  `setting_value` TEXT NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- Seed Data: Admin, Settings, Athletes, Contracts, News, Demo User
-- --------------------------------------------------------------------
INSERT INTO `admins` (`username`, `password_hash`, `full_name`, `role`) VALUES
('admin', '$2y$10$wE9zR2e5Y99kK4jH0zP3IeKx7hY81M2Uv3A2Vq9J9.m2oB8m4rZgG', 'National Sports Authority Officer', 'SuperAdmin');

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('registration_status', 'open'),
('event_name', '8th Khelo India Youth Games (KIYG 2026)'),
('event_host', 'Telangana (6 Host Cities)'),
('event_dates', 'December 11 - 22, 2026'),
('scholarship_grant', '₹5,00,000 annually for 8 years (Total ₹40,00,000)'),
('trading_engine_status', 'LIVE');

-- Sample Demo User (Default Login: user@kheloindia.com / password: password123)
INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password_hash`, `phone`, `wallet_balance`, `favorite_sports`, `kyc_status`) VALUES
(1, 'Vikramaditya Roy', 'vikram_trader', 'user@kheloindia.com', '$2y$10$tZz27U619a9iF2fKskg/k.c6M08iGZ0zO.iYJt0c/h6E.M4z4G4Iq', '9876500001', 25000.00, 'Athletics,Kalaripayattu,Swimming,Mallakhamba', 'Verified');

INSERT INTO `wallet_transactions` (`user_id`, `amount`, `type`, `description`, `balance_after`, `txn_ref`) VALUES
(1, 10000.00, 'SIGNUP_BONUS', 'Super Sapiens Welcome Demo Trading Credits', 10000.00, 'TXN-INIT-1001'),
(1, 15000.00, 'DEPOSIT', 'Instant UPI Add Funds via GPay/PhonePe', 25000.00, 'TXN-UPI-982314');

-- Initial Athletes
INSERT INTO `athletes` (
  `id`, `application_id`, `full_name`, `dob`, `gender`, `category`, `aadhaar_hash`, `guardian_name`,
  `phone`, `email`, `state`, `district`, `school_name`, `board`, `age_category`,
  `sport_discipline`, `sub_event`, `playing_experience`, `qualification_level`,
  `achievement_details`, `coach_name`, `coach_phone`, `certificate_no`, `status`,
  `trial_venue`, `trial_date`, `talent_score`, `avatar_icon`
) VALUES
(
  1, 'KISG26-784920', 'Aarav Sharma', '2009-04-15', 'Male', 'General',
  SHA2('998877661101', 256), 'Rajesh Sharma', '9876543210', 'aarav.sharma@example.com',
  'Telangana', 'Hyderabad', 'Hyderabad Public School, Begumpet', 'CBSE', 'Under-17',
  'Athletics', '100m Sprint & Long Jump', 4, 'SGFI National School Games',
  'Gold Medal in 100m at Telangana State School Games 2025 (10.92s). SGFI National finalist.',
  'P. Gopichand Academy / Coach N. Rao', '9848012345', 'SGFI-TEL-2025-089', 'Shortlisted for Trials',
  'GMC Balayogi Athletic Stadium, Gachibowli, Hyderabad', '2026-11-28', 96.4, '🏃'
),
(
  2, 'KISG26-651239', 'Ananya Nair', '2008-09-22', 'Female', 'OBC',
  SHA2('887766552202', 256), 'Sunil Nair', '9811223344', 'ananya.sports@example.com',
  'Kerala', 'Kozhikode', 'St. Joseph Higher Secondary School', 'State Board', 'Under-17',
  'Kalaripayattu', 'Chuvadukal & Meypayattu (Indigenous)', 6, 'State Level Medalist',
  'First Place at Kerala State Kalaripayattu Championship 2025. Master of Urumi & sword forms.',
  'Gurukkal Madhavan', '9447012345', 'KALA-KER-2025-412', 'Verified',
  'LNCPE Karyavattom, Thiruvananthapuram', '2026-12-02', 94.8, '⚔️'
),
(
  3, 'KISG26-903417', 'Jaspreet Singh', '2007-01-10', 'Male', 'General',
  SHA2('776655443303', 256), 'Harbhajan Singh', '9899112233', 'jaspreet.gatka@example.com',
  'Punjab', 'Amritsar', 'Khalsa Collegiate Senior Secondary School', 'State Board', 'Under-21',
  'Gatka', 'Single Soti & Fari-Soti (Indigenous)', 5, 'National Federation Cup',
  'Silver Medalist at All India National Gatka Championship 2025.',
  'Ustad Gurmukh Singh', '9872012345', 'GATKA-PB-2025-78', 'Shortlisted for Trials',
  'Guru Nanak Stadium, Amritsar', '2026-11-30', 91.2, '🛡️'
),
(
  4, 'KISG26-442189', 'Meera Patel', '2010-06-18', 'Female', 'General',
  SHA2('665544332204', 256), 'Deepak Patel', '9825012345', 'meera.swimming@example.com',
  'Gujarat', 'Ahmedabad', 'Delhi Public School, Bopal', 'CBSE', 'Under-17',
  'Swimming', '100m Butterfly & 200m Medley', 5, 'CBSE National Sports Meet',
  'Gold Medal in 100m Butterfly at CBSE Nationals 2025 (58.4s).',
  'Coach Kamlesh Joshi', '9825198765', 'CBSE-SWIM-2025-104', 'Shortlisted for Trials',
  'Sardar Patel Swimming Complex, Ahmedabad', '2026-12-05', 95.1, '🏊'
),
(
  5, 'KISG26-318520', 'Rohan Deshmukh', '2008-11-05', 'Male', 'General',
  SHA2('554433221105', 256), 'Sanjay Deshmukh', '9822099887', 'rohan.mallakhamba@example.com',
  'Maharashtra', 'Pune', 'Jnana Prabodhini Prashala, Sadashiv Peth', 'State Board', 'Under-17',
  'Mallakhamba', 'Pole & Rope Mallakhamba (Indigenous)', 7, 'SGFI National School Games',
  'National Bronze Medalist in Pole Mallakhamba at SGFI 2024-25. Acrobatic mastery on vertical teak wood pole.',
  'Vyayamshala Coach V. Kulkarni', '9822112233', 'MAL-MH-2025-019', 'Shortlisted for Trials',
  'Shree Shiv Chhatrapati Sports Complex, Balewadi, Pune', '2026-11-29', 93.6, '🚩'
);

-- Athlete Contracts (F&O Style Trading Instruments)
INSERT INTO `athlete_contracts` (
  `id`, `athlete_id`, `ticker`, `contract_title`, `current_price`, `previous_close`,
  `high_24h`, `low_24h`, `volume_contracts`, `open_interest`, `contract_expiry`, `lot_size`
) VALUES
(
  1, 1, 'AARAV-100M-DEC26', 'Aarav Sharma • 100m Sprint Gold Futures Dec 26',
  248.50, 221.00, 255.00, 218.00, 6840, 3420, '2026-12-22', 10
),
(
  2, 2, 'ANANYA-KALA-DEC26', 'Ananya Nair • Kalaripayattu Championship Futures',
  182.00, 165.50, 189.00, 162.00, 4210, 2100, '2026-12-22', 10
),
(
  3, 3, 'JASPREET-GATKA-DEC26', 'Jaspreet Singh • Gatka Soti Performance Contract',
  145.00, 152.00, 155.00, 140.00, 3120, 1840, '2026-12-22', 10
),
(
  4, 4, 'MEERA-SWIM-DEC26', 'Meera Patel • 100m Butterfly Podium Futures',
  290.00, 274.00, 302.00, 270.00, 8920, 4510, '2026-12-22', 10
),
(
  5, 5, 'ROHAN-MAL-DEC26', 'Rohan Deshmukh • Pole Mallakhamba National Contract',
  195.50, 188.00, 201.00, 185.00, 5130, 2900, '2026-12-22', 10
);

-- Sample Open Trades for Demo User (id=1)
INSERT INTO `trades` (
  `user_id`, `contract_id`, `order_type`, `direction`, `quantity`, `entry_price`, `current_price`, `margin_used`, `status`, `pnl`
) VALUES
(1, 1, 'MARKET', 'BUY', 20, 230.00, 248.50, 4600.00, 'OPEN', 370.00),
(1, 4, 'MARKET', 'BUY', 10, 280.00, 290.00, 2800.00, 'OPEN', 100.00);

-- Personalized & Algorithmic News Feed
INSERT INTO `news_feed` (`athlete_id`, `title`, `summary`, `category`, `sentiment`, `impact_pct`, `published_at`) VALUES
(
  1,
  'Aarav Sharma Clocks 10.92s in Telangana 100m State Heats; Contract Surges +12.4%',
  'Aarav Sharma showcased blistering acceleration at the GMC Balayogi Athletic Stadium, cementing his spot as the top Under-17 sprint seed for KIYG 2026.',
  'Trials', 'BULLISH', 12.4, NOW() - INTERVAL 1 HOUR
),
(
  4,
  'Meera Patel Smashes Western Zone Butterfly Record with 58.4s Split',
  '16-year-old Meera Patel broke the regional scholastic record, prompting a rush of long call options ahead of the national school sports trials.',
  'Record', 'BULLISH', 5.8, NOW() - INTERVAL 3 HOUR
),
(
  2,
  'Ananya Nair Inducted into LNCPE Karyavattom High-Performance Indigenous Squad',
  'Following her gold in Kozhikode, Ananya receives specialized coaching under master Gurukkals for the 2026 Khelo India Youth Games.',
  'KISCE', 'BULLISH', 9.9, NOW() - INTERVAL 6 HOUR
),
(
  3,
  'Jaspreet Singh Faces Gripping Combat Challenge in Amritsar Quarterfinals',
  'Gatka single soti clash ends in tight points decision; contract consolidates around ₹145 as traders eye upcoming national finals.',
  'Scouting', 'NEUTRAL', -4.6, NOW() - INTERVAL 12 HOUR
),
(
  5,
  'Rohan Deshmukh Unveils High-Difficulty Inverted Pole Sequence in Pune',
  'Maharashtra scouts awarded Rohan a 93.6 talent rating for acrobatic rope transition, sparking increased volume in ROHAN-MAL-DEC26 contracts.',
  'Medal', 'BULLISH', 4.0, NOW() - INTERVAL 1 DAY
);
