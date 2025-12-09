-- Tour versions core table
CREATE TABLE IF NOT EXISTS tour_versions (
  version_id INT AUTO_INCREMENT PRIMARY KEY,
  tour_id INT NOT NULL,
  version_name VARCHAR(255) NOT NULL,
  version_type VARCHAR(32) NOT NULL, -- season|promo|special (free text for flexibility)
  start_date DATE NULL,
  end_date DATE NULL,
  description TEXT NULL,
  status ENUM('hidden','visible','archived') DEFAULT 'hidden',
  is_active TINYINT(1) DEFAULT 0,
  activation_mode ENUM('manual','scheduled') DEFAULT 'manual',
  scheduled_at DATETIME NULL,
  activated_at DATETIME NULL,
  source_type ENUM('tour','version') NULL,
  source_id INT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tour_version_name (tour_id, version_name),
  INDEX idx_tour_id (tour_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Itineraries (per version)
CREATE TABLE IF NOT EXISTS tour_version_itineraries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  version_id INT NOT NULL,
  day_number INT NOT NULL DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  accommodation VARCHAR(255) NULL,
  INDEX idx_version (version_id),
  CONSTRAINT fk_version_itineraries FOREIGN KEY (version_id)
    REFERENCES tour_versions(version_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Prices (per version)
CREATE TABLE IF NOT EXISTS tour_version_prices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  version_id INT NOT NULL,
  package_name VARCHAR(255) NOT NULL,
  price_adult DECIMAL(12,2) DEFAULT 0,
  price_child DECIMAL(12,2) DEFAULT 0,
  price_infant DECIMAL(12,2) DEFAULT 0,
  description TEXT NULL,
  INDEX idx_version (version_id),
  CONSTRAINT fk_version_prices FOREIGN KEY (version_id)
    REFERENCES tour_versions(version_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Media (per version)
CREATE TABLE IF NOT EXISTS tour_version_media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  version_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  media_type VARCHAR(32) DEFAULT 'image',
  is_featured TINYINT(1) DEFAULT 0,
  INDEX idx_version (version_id),
  CONSTRAINT fk_version_media FOREIGN KEY (version_id)
    REFERENCES tour_versions(version_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rules (apply conditions)
CREATE TABLE IF NOT EXISTS tour_version_rules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  version_id INT NOT NULL,
  rule_type VARCHAR(64) NOT NULL, -- date|coupon|min_group|other
  rule_value VARCHAR(128) NULL,
  rule_params TEXT NULL, -- JSON for extra params
  INDEX idx_version (version_id),
  CONSTRAINT fk_version_rules FOREIGN KEY (version_id)
    REFERENCES tour_versions(version_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
