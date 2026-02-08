<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    // Add index for leaderboard performance
    // Using end_time instead of completed_at based on existing schema
    $sql = "CREATE INDEX idx_score_endtime ON quiz_attempts (score, end_time)";
    
    try {
        $pdo->exec($sql);
        echo "Index 'idx_score_endtime' created successfully on quiz_attempts table.";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "Index 'idx_score_endtime' already exists.";
        } else {
            throw $e;
        }
    }

} catch (PDOException $e) {
    echo "Error adding index: " . $e->getMessage();
}
?>
