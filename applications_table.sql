-- ============================================================
-- Run this in phpMyAdmin → devboard database → SQL tab
-- ============================================================

CREATE TABLE applications (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    job_id        INT NOT NULL,
    user_id       INT NOT NULL,
    full_name     VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL,
    phone         VARCHAR(30)  NOT NULL,
    cover_letter  TEXT,
    cv_filename   VARCHAR(255) NOT NULL,
    cv_path       VARCHAR(255) NOT NULL,
    status        ENUM('pending','reviewed','shortlisted','rejected') DEFAULT 'pending',
    applied_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id)  REFERENCES jobs(id)  ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
