<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    // Add columns if they don't exist
    $columns = [
        'full_name' => "VARCHAR(100) DEFAULT NULL",
        'phone' => "VARCHAR(20) DEFAULT NULL",
        'profile_photo' => "VARCHAR(255) DEFAULT NULL"
    ];

    foreach ($columns as $col => $def) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN $col $def");
            echo "Added column $col successfully.<br>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "Column $col already exists.<br>";
            } else {
                throw $e;
            }
        }
    }
    
    echo "Users table schema updated.";

} catch (PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>
