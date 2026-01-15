-- update_schema.sql

-- 1. Update Quizzes Table
ALTER TABLE quizzes 
ADD COLUMN max_attempts INT DEFAULT 0 COMMENT '0 for unlimited',
ADD COLUMN enable_certificate TINYINT(1) DEFAULT 0;

-- 2. Update Quiz Attempts Table
ALTER TABLE quiz_attempts
ADD COLUMN attempt_number INT DEFAULT 1;

-- 3. Create Certificates Table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    certificate_code VARCHAR(50) NOT NULL UNIQUE,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);
