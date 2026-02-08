<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch User Details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection would go here
    
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $profile_photo = $user['profile_photo'];

    // Validation
    if (!empty($phone) && !is_numeric($phone)) {
        $error = "Phone number must be numeric.";
    } else {
        // Handle File Upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
            $fileName = $_FILES['profile_photo']['name'];
            $fileSize = $_FILES['profile_photo']['size'];
            $fileType = $_FILES['profile_photo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
            
            if (in_array($fileExtension, $allowedfileExtensions)) {
                if ($fileSize < 2097152) { // 2MB
                    // Directory
                    $uploadFileDir = '../uploads/profiles/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    
                    $newFileName = time() . '_' . $user_id . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $profile_photo = $newFileName;
                    } else {
                        $error = 'There was some error moving the file to upload directory.';
                    }
                } else {
                    $error = 'Upload failed. Allowed file size: 2MB';
                }
            } else {
               $error = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
            }
        }

        if (empty($error)) {
            $updateSql = "UPDATE users SET full_name = ?, phone = ?, profile_photo = ? WHERE id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            if ($updateStmt->execute([$full_name, $phone, $profile_photo, $user_id])) {
                $_SESSION['success'] = "Profile updated successfully!";
                redirect('user/profile.php');
            } else {
                $error = "Failed to update profile.";
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg" style="border-radius: 15px;">
            <div class="card-header bg-white border-bottom-0 py-4 px-5">
                <h4 class="mb-0 fw-bold text-primary">Edit Profile</h4>
            </div>
            <div class="card-body p-5">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                             <?php 
                                $photoPath = !empty($user['profile_photo']) ? '../uploads/profiles/' . $user['profile_photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']);
                            ?>
                            <img src="<?php echo htmlspecialchars($photoPath); ?>" id="preview" class="rounded-circle border border-3 border-white shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            <label for="profile_photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; cursor: pointer;">
                                <i class="material-icons-round fs-6">edit</i>
                            </label>
                            <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <p class="text-secondary small mt-2">Allowed: JPG, PNG. Max 2MB.</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="full_name" class="form-control form-control-lg" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" placeholder="Enter your full name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="tel" name="phone" class="form-control form-control-lg" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Enter phone number">
                    </div>

                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">Save Changes</button>
                        <a href="profile.php" class="btn btn-light btn-lg rounded-pill text-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
