<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS `user_answers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `quiz_id` int(11) NOT NULL,
        `question_id` int(11) NOT NULL,
        `selected_option_id` int(11) DEFAULT NULL,
        `attempt_id` int(11) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `quiz_id` (`quiz_id`),
        KEY `attempt_id` (`attempt_id`), 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $pdo->exec($sql);
    echo "Table user_answers created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
