-- Demo Data for Quiz Platform
-- Run this in your MySQL client

USE quiz_platform;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Categories
INSERT INTO categories (id, name, description, status) VALUES 
(1, 'General Knowledge', 'Test your daily awareness and world knowledge.', 'active'),
(2, 'Competitive Exams', 'Preparation for government and banking exams.', 'active'),
(3, 'Programming Basics', 'Learn the fundamentals of coding.', 'active')
ON DUPLICATE KEY UPDATE name=name;

-- 2. Quizzes
-- Category 1: GK
INSERT INTO quizzes (id, category_id, title, description, price, time_limit, total_marks, pass_percentage, status) VALUES
(1, 1, 'GK Level 1', 'Basic general knowledge questions for beginners.', 5.00, 10, 5, 40, 'active'),
(2, 1, 'GK Level 2', 'Intermediate level current affairs.', 10.00, 15, 5, 50, 'active');

-- Category 2: Competitive Exams
INSERT INTO quizzes (id, category_id, title, description, price, time_limit, total_marks, pass_percentage, status) VALUES
(3, 2, 'TNPSC Model Test', 'Sample questions for TNPSC Group 4.', 15.00, 20, 5, 60, 'active'),
(4, 2, 'Banking Aptitude Test', 'Numerical ability and reasoning for banking.', 20.00, 25, 5, 60, 'active');

-- Category 3: Programming (Free)
INSERT INTO quizzes (id, category_id, title, description, price, time_limit, total_marks, pass_percentage, status) VALUES
(5, 3, 'PHP Basics Quiz', 'Core concepts of PHP scripting.', 0.00, 15, 5, 50, 'active'),
(6, 3, 'HTML & CSS Quiz', 'Frontend fundamentals.', 0.00, 15, 5, 50, 'active');

-- 3. Questions & Options
-- Quiz 1: GK Level 1
INSERT INTO questions (id, quiz_id, question_text, question_type, marks) VALUES
(1, 1, 'What is the capital of India?', 'text', 1),
(2, 1, 'Which planet is known as the Red Planet?', 'text', 1),
(3, 1, 'Who is known as the Iron Man of India?', 'text', 1),
(4, 1, 'What is the national animal of India?', 'text', 1),
(5, 1, 'Identify this famous monument.', 'image', 1); -- Placeholder image usage

INSERT INTO options (question_id, option_text, is_correct) VALUES
(1, 'Mumbai', 0), (1, 'New Delhi', 1), (1, 'Kolkata', 0), (1, 'Chennai', 0),
(2, 'Venus', 0), (2, 'Mars', 1), (2, 'Jupiter', 0), (2, 'Saturn', 0),
(3, 'Sardar Vallabhbhai Patel', 1), (3, 'Gandhi', 0), (3, 'Nehru', 0), (3, 'Bose', 0),
(4, 'Lion', 0), (4, 'Tiger', 1), (4, 'Elephant', 0), (4, 'Peacock', 0),
(5, 'Taj Mahal', 1), (5, 'Qutub Minar', 0), (5, 'Red Fort', 0), (5, 'Gateway of India', 0);


-- Quiz 5: PHP Basics
INSERT INTO questions (id, quiz_id, question_text, question_type, marks) VALUES
(6, 5, 'Which tag is used for PHP output?', 'text', 1),
(7, 5, 'What does PHP stand for?', 'text', 1),
(8, 5, 'Which symbol starts a variable in PHP?', 'text', 1),
(9, 5, 'How do you end a PHP statement?', 'text', 1),
(10, 5, 'Which function outputs formatted text?', 'text', 1);

INSERT INTO options (question_id, option_text, is_correct) VALUES
(6, 'echo', 1), (6, 'print', 0), (6, 'output', 0), (6, 'write', 0),
(7, 'Personal Home Page', 0), (7, 'Hypertext Preprocessor', 1), (7, 'Private Hosting Page', 0), (7, 'Public Host Platform', 0),
(8, '@', 0), (8, '$', 1), (8, '%', 0), (8, '&', 0),
(9, '.', 0), (9, ':', 0), (9, ';', 1), (9, '>', 0),
(10, 'printf()', 1), (10, 'print()', 0), (10, 'echo()', 0), (10, 'write()', 0);

SET FOREIGN_KEY_CHECKS = 1;
