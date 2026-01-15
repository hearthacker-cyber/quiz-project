<?php
// seed_demo_data.php
require_once 'config/database.php';

echo "Starting Data Seeding...\n";

// Helper function to get or insert category
function getOrInsertCategory($pdo, $name, $desc, $status = 'active') {
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$name]);
    $cat = $stmt->fetch();
    if ($cat) return $cat['id'];
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, ?)");
    $stmt->execute([$name, $desc, $status]);
    return $pdo->lastInsertId();
}

// Helper for quiz
function insertQuiz($pdo, $catId, $title, $data) {
    // Check if exists to avoid duplicates
    $stmt = $pdo->prepare("SELECT id FROM quizzes WHERE title = ? AND category_id = ?");
    $stmt->execute([$title, $catId]);
    if ($stmt->fetch()) return false;

    $sql = "INSERT INTO quizzes (category_id, title, description, price, time_limit, total_marks, pass_percentage, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $catId, 
        $title, 
        $data['description'], 
        $data['price'], 
        $data['time_limit'], 
        $data['total_marks'], 
        $data['pass_percentage'], 
        'active'
    ]);
    return $pdo->lastInsertId();
}

function insertQuestion($pdo, $quizId, $text, $type, $marks, $options, $correctIndex) {
    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_type, marks) VALUES (?, ?, ?, ?)");
    $stmt->execute([$quizId, $text, $type, $marks]);
    $qId = $pdo->lastInsertId();

    foreach ($options as $index => $optText) {
        $isCorrect = ($index === $correctIndex) ? 1 : 0;
        $stmtOpt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
        $stmtOpt->execute([$qId, $optText, $isCorrect]);
    }
}

// --- DATA DEFINITION ---

$dataStructure = [
    'General Knowledge' => [
        'type' => 'paid',
        'desc' => 'Test your daily awareness.',
        'quizzes' => [
            [
                'title' => 'GK Level 1',
                'description' => 'Beginner general knowledge.',
                'price' => 5.00,
                'time' => 10,
                'questions' => [
                    ['What is the capital of France?', 'text', ['Berlin', 'Madrid', 'Paris', 'Rome'], 2],
                    ['Largest ocean in the world?', 'text', ['Atlantic', 'Indian', 'Pacific', 'Arctic'], 2],
                    ['H2O is the chemical formula for?', 'text', ['Oxygen', 'Water', 'Hydrogen', 'Ice'], 1],
                    ['Which is the smallest continent?', 'text', ['Asia', 'Europe', 'Australia', 'Africa'], 2],
                    ['Number of days in a leap year?', 'text', ['365', '366', '364', '360'], 1]
                ]
            ],
            [
                'title' => 'GK Level 2',
                'description' => 'Intermediate questions.',
                'price' => 10.00,
                'time' => 15,
                'questions' => [
                    ['First man to step on the Moon?', 'text', ['Yuri Gagarin', 'Neil Armstrong', 'Buzz Aldrin', 'Michael Collins'], 1],
                    ['Currency of Japan?', 'text', ['Yuan', 'Won', 'Yen', 'Dollar'], 2],
                    ['Capital of Australia?', 'text', ['Sydney', 'Melbourne', 'Canberra', 'Perth'], 2],
                    ['Who wrote "Romeo and Juliet"?', 'text', ['Dickens', 'Shakespeare', 'Hemingway', 'Austen'], 1],
                    ['Hardest natural substance?', 'text', ['Gold', 'Iron', 'Diamond', 'Platinum'], 2]
                ]
            ]
        ]
    ],
    'Competitive Exams' => [
        'type' => 'paid',
        'desc' => 'Exam prep material.',
        'quizzes' => [
            [
                'title' => 'TNPSC Model Test',
                'description' => 'Group 4 model questions.',
                'price' => 15.00,
                'time' => 20,
                'questions' => [
                    ['First Governor General of Independent India?', 'text', ['Nehru', 'Mountbatten', 'Rajaji', 'Prasad'], 1],
                    ['Thirukkural has how many chapters?', 'text', ['133', '1330', '13', '330'], 0],
                    ['Fundamental Rights are in which part of Constitution?', 'text', ['Part I', 'Part II', 'Part III', 'Part IV'], 2],
                    ['Father of Green Revolution in India?', 'text', ['Verghese Kurien', 'M.S. Swaminathan', 'Norman Borlaug', 'Amartya Sen'], 1],
                    ['RBI Headquarters is in?', 'text', ['Delhi', 'Chennai', 'Kolkata', 'Mumbai'], 3]
                ]
            ]
        ]
    ],
    'Programming Basics' => [
        'type' => 'free',
        'desc' => 'Coding fundamentals.',
        'quizzes' => [
            [
                'title' => 'HTML & CSS Quiz',
                'description' => 'Web design basics.',
                'price' => 0.00,
                'time' => 15,
                'questions' => [
                    ['HTML stands for?', 'text', ['Hyper Text Markup Language', 'High Text Machine Language', 'Hyper Tool Multi Language', 'None'], 0],
                    ['Which tag is used for largest heading?', 'text', ['<h6>', '<head>', '<h1>', '<header>'], 2],
                    ['CSS stands for?', 'text', ['Creative Style Sheets', 'Cascading Style Sheets', 'Colorful Style Sheets', 'Computer Style Sheets'], 1],
                    ['Which property changes text color?', 'text', ['text-color', 'font-color', 'color', 'fg-color'], 2],
                    ['To make text bold, we use?', 'text', ['<b>', '<bold>', '<bb>', '<dark>'], 0]
                ]
            ]
        ]
    ]
];

// --- EXECUTION ---

try {
    $pdo->beginTransaction();

    foreach ($dataStructure as $catName => $catData) {
        $catId = getOrInsertCategory($pdo, $catName, $catData['desc']);
        echo "Category: $catName (ID: $catId)\n";

        foreach ($catData['quizzes'] as $quizData) {
            
            $qInfo = [
                'description' => $quizData['description'],
                'price' => $quizData['price'],
                'time_limit' => $quizData['time'],
                'total_marks' => count($quizData['questions']), // 1 mark each
                'pass_percentage' => 50
            ];

            $quizId = insertQuiz($pdo, $catId, $quizData['title'], $qInfo);
            
            if ($quizId) {
                echo "  + Created Quiz: {$quizData['title']} (ID: $quizId)\n";
                foreach ($quizData['questions'] as $q) {
                    insertQuestion($pdo, $quizId, $q[0], $q[1], 1, $q[2], $q[3]);
                }
            } else {
                echo "  - Skipped Quiz: {$quizData['title']} (Already exists)\n";
            }
        }
    }

    $pdo->commit();
    echo "\nSeeding Completed Successfully!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Error: " . $e->getMessage());
}
?>
