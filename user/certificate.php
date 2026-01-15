<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/auth.php';

requireLogin();

$code = $_GET['code'] ?? '';

if (!$code) {
    die("Invalid Certificate Code");
}

// Fetch Certificate Details
$sql = "SELECT c.*, u.name as user_name, q.title as quiz_title, q.pass_percentage 
        FROM certificates c 
        JOIN users u ON c.user_id = u.id 
        JOIN quizzes q ON c.quiz_id = q.id 
        WHERE c.certificate_code = ? AND c.user_id = ?"; // Security: User must own it

$stmt = $pdo->prepare($sql);
$stmt->execute([$code, $_SESSION['user_id']]);
$cert = $stmt->fetch();

if (!$cert) {
    die("Certificate not found or unauthorized.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - <?php echo htmlspecialchars($cert['certificate_code']); ?></title>
    <style>
        body { margin: 0; padding: 0; background: #ccc; font-family: 'Georgia', serif; }
        .cert-container {
            width: 800px;
            height: 600px;
            background: #fff;
            margin: 20px auto;
            padding: 50px;
            text-align: center;
            border: 10px double #1a237e;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
        .cert-header { font-size: 40px; color: #1a237e; font-weight: bold; margin-bottom: 20px; }
        .cert-subheader { font-size: 20px; color: #555; }
        .cert-name { font-size: 32px; color: #000; border-bottom: 1px solid #999; display: inline-block; padding: 10px 40px; margin: 20px 0; font-weight: bold; }
        .cert-body { font-size: 18px; line-height: 1.6; color: #333; margin-bottom: 40px; }
        .cert-footer { display: flex; justify-content: space-between; margin-top: 50px; padding: 0 40px; }
        .signature { border-top: 1px solid #333; padding-top: 10px; width: 200px; }
        .cert-id { position: absolute; bottom: 10px; right: 20px; font-size: 12px; color: #999; }
        
        @media print {
            body { background: #fff; }
            .cert-container { margin: 0; border: 5px double #1a237e; width: 100%; height: 100%; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="cert-container">
    <div class="cert-header">Certificate of Completion</div>
    <div class="cert-subheader">This is to certify that</div>
    
    <div class="cert-name"><?php echo htmlspecialchars($cert['user_name']); ?></div>
    
    <div class="cert-body">
        has successfully completed the quiz<br>
        <strong><?php echo htmlspecialchars($cert['quiz_title']); ?></strong><br>
        on <?php echo date('F j, Y', strtotime($cert['issued_at'])); ?>.
    </div>
    
    <div class="cert-footer">
        <div class="signature">Authorized Signature</div>
        <div class="signature"><?php echo APP_NAME; ?></div>
    </div>
    
    <div class="cert-id">Certificate ID: <?php echo htmlspecialchars($cert['certificate_code']); ?></div>
</div>

<div style="text-align: center; margin-top: 20px;" class="no-print">
    <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Download / Print PDF</button>
</div>

</body>
</html>
