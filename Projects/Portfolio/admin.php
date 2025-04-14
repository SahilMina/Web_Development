<?php
require_once "config.php";

// Initialize variables
$username = $password = "";
$username_err = $password_err = $login_err = "";
$message = "";
$is_logged_in = false;

// Handle logout
if(isset($_GET["logout"])){
    // Clear all session variables
    $_SESSION = array();
    
    // If it's desired to kill the session, also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: admin.php");
    exit;
}

// Check if user is already logged in
if(isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true){
    $is_logged_in = true;
} else {
    // Process login form submission
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login_submit"])){
        
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter username.";
        } else{
            $username = trim($_POST["username"]);
        }
        
        // Check if password is empty
        if(empty(trim($_POST["password"]))){
            $password_err = "Please enter your password.";
        } else{
            $password = trim($_POST["password"]);
        }
        
        // Validate credentials
        if(empty($username_err) && empty($password_err)){
            // Prepare a select statement
            $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
            
            if($stmt = mysqli_prepare($conn, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                
                // Set parameters
                $param_username = $username;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Store result
                    mysqli_stmt_store_result($stmt);
                    
                    // Check if username exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1){                    
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                // Password is correct
                                // Regenerate session ID for security
                                session_regenerate_id(true);
                                
                                // Store data in session variables
                                $_SESSION["admin_logged_in"] = true;
                                $_SESSION["admin_id"] = $id;
                                $_SESSION["admin_username"] = $username;
                                
                                // Update last login time
                                $update_sql = "UPDATE admin_users SET last_login = NOW() WHERE id = ?";
                                if($update_stmt = mysqli_prepare($conn, $update_sql)){
                                    mysqli_stmt_bind_param($update_stmt, "i", $id);
                                    mysqli_stmt_execute($update_stmt);
                                    mysqli_stmt_close($update_stmt);
                                }
                                
                                // Set logged in status for this page load
                                $is_logged_in = true;
                            } else{
                                // Password is not valid
                                $login_err = "Invalid username or password.";
                            }
                        }
                    } else{
                        // Username doesn't exist
                        $login_err = "Invalid username or password.";
                    }
                } else{
                    $login_err = "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Admin panel functionality (only processed when logged in)
if($is_logged_in) {
    $message = "";

    // Handle image upload
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])){
        $target_dir = "uploads/gallery/";
        
        // Create directory if it doesn't exist
        if(!file_exists($target_dir)){
            mkdir($target_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $uploadOk = 1;
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            $message = "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check file size (5MB max)
        if ($_FILES["image"]["size"] > 5000000) {
            $message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if(!in_array($file_extension, $allowed_types)) {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Set correct permissions
                chmod($target_file, 0644);
                
                $title = $_POST["title"];
                $description = $_POST["description"];
                $visible = isset($_POST["is_visible"]) ? 1 : 0;
                
                // Store path in database
                $relative_path = $target_file;
                
                // Using updated table structure:
                // id, title, description, image_path, visible, upload_date
                $sql = "INSERT INTO gallery (title, description, image_path, visible) VALUES (?, ?, ?, ?)";
                if($stmt = mysqli_prepare($conn, $sql)){
                    mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $relative_path, $visible);
                    if(mysqli_stmt_execute($stmt)){
                        $message = "Image uploaded successfully.";
                    } else{
                        $message = "Error uploading to database: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Fetch skills for management
    $skills_query = "SELECT * FROM skills ORDER BY category, level DESC";
    $skills_result = mysqli_query($conn, $skills_query);

    // Handle skill addition
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_skill"])){
        $skill_name = $_POST["skill_name"];
        $skill_level = $_POST["skill_level"];
        $skill_category = $_POST["skill_category"];
        
        $sql = "INSERT INTO skills (name, level, category) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sis", $skill_name, $skill_level, $skill_category);
            if(mysqli_stmt_execute($stmt)){
                $message = "Skill added successfully.";
            } else{
                $message = "Error adding skill: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Handle skill deletion
    if(isset($_GET["delete_skill"])){
        $id = intval($_GET["delete_skill"]);
        
        $sql = "DELETE FROM skills WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $id);
            if(mysqli_stmt_execute($stmt)){
                $message = "Skill deleted successfully.";
            } else {
                $message = "Error deleting skill: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Handle delete request
    if(isset($_GET["delete"])){
        $id = intval($_GET["delete"]);
        
        // Get image path before deleting record
        $sql = "SELECT image_path FROM gallery WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $image_path);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            // Delete record from database
            $sql = "DELETE FROM gallery WHERE id = ?";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "i", $id);
                if(mysqli_stmt_execute($stmt)){
                    // Try to delete the file if exists
                    if(file_exists($image_path)){
                        unlink($image_path);
                    }
                    $message = "Item deleted successfully.";
                } else {
                    $message = "Error deleting item: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    // Handle visibility toggle
    if(isset($_GET["toggle"])){
        $id = intval($_GET["toggle"]);
        $sql = "UPDATE gallery SET visible = NOT visible WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $id);
            if(mysqli_stmt_execute($stmt)){
                $message = "Visibility updated.";
            } else {
                $message = "Error updating visibility: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Function to convert level percentage to label
    function getSkillLevelLabel($percentage) {
        if ($percentage < 25) {
            return 'Noob';
        } else if ($percentage < 50) {
            return 'Beginner';
        } else if ($percentage < 75) {
            return 'Intermediate';
        } else {
            return 'Advanced';
        }
    }

    // Fetch all gallery items
    $gallery_query = "SELECT * FROM gallery ORDER BY upload_date DESC";
    $gallery_result = mysqli_query($conn, $gallery_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_logged_in ? "Admin Panel" : "Admin Login"; ?></title>
    <link rel="stylesheet" href="styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php if($is_logged_in): ?>
            <!-- Admin Panel Content -->
            <div class="admin-header">
                <h1>Admin Panel</h1>
                <div class="admin-nav">
                    <a href="index.php">View Site</a>
                    <a href="?logout=true">Logout</a>
                </div>
            </div>
            
            <?php if($message): ?>
                <div class="alert <?php echo strpos($message, "Error") !== false ? 'error' : ''; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="tabs">
                <button class="tab active" data-tab="gallery">Gallery Management</button>
                <button class="tab" data-tab="skills">Skills Management</button>
            </div>
            
            <div class="admin-panel">
                <div class="tab-content active" id="gallery-tab">
                    <div class="upload-form">
                        <h2>Upload New Image</h2>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Image</label>
                                <input type="file" id="image" name="image" accept="image/*" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="is_visible" checked>
                                    Visible to visitors
                                </label>
                            </div>
                            
                            <button type="submit" class="btn">Upload</button>
                        </form>
                    </div>
                    
                    <div class="gallery-manager">
                        <h2>Manage Gallery</h2>
                        
                        <?php if($gallery_result && mysqli_num_rows($gallery_result) > 0): ?>
                            <?php while($item = mysqli_fetch_assoc($gallery_result)): ?>
                                <div class="admin-gallery-item">
                                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    
                                    <div class="item-info">
                                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                        <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . (strlen($item['description']) > 100 ? '...' : ''); ?></p>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <a href="?toggle=<?php echo $item['id']; ?>" class="action-btn toggle-btn <?php echo $item['visible'] ? '' : 'hidden'; ?>" title="<?php echo $item['visible'] ? 'Hide' : 'Show'; ?>">
                                            <i class="fas <?php echo $item['visible'] ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                        </a>
                                        <a href="?delete=<?php echo $item['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this item?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No gallery items found. Upload some images to get started.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="tab-content" id="skills-tab">
                    <div class="upload-form">
                        <h2>Add New Skill</h2>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="skill_name">Skill Name</label>
                                <input type="text" id="skill_name" name="skill_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="skill_level">Skill Level (0-100)</label>
                                <input type="number" id="skill_level" name="skill_level" min="0" max="100" required>
                                <div class="skill-level-info">
                                    <span>0-24: Noob</span> |
                                    <span>25-49: Beginner</span> |
                                    <span>50-74: Intermediate</span> |
                                    <span>75-100: Advanced</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="skill_category">Category</label>
                                <input type="text" id="skill_category" name="skill_category" required>
                            </div>
                            
                            <input type="hidden" name="add_skill" value="1">
                            <button type="submit" class="btn">Add Skill</button>
                        </form>
                    </div>
                    
                    <div class="skills-manager">
                        <h2>Manage Skills</h2>
                        
                        <?php if($skills_result && mysqli_num_rows($skills_result) > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Skill</th>
                                        <th>Category</th>
                                        <th>Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($skill = mysqli_fetch_assoc($skills_result)): ?>
                                        <?php 
                                        $levelLabel = getSkillLevelLabel($skill['level']);
                                        $levelClass = strtolower($levelLabel);
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($skill['name']); ?></td>
                                            <td><?php echo htmlspecialchars($skill['category']); ?></td>
                                            <td>
                                                <span class="skill-level-badge skill-level-<?php echo $levelClass; ?>">
                                                    <?php echo $levelLabel; ?>
                                                </span>
                                                <div class="skill-level-bar">
                                                    <div class="skill-level-progress" style="width: <?php echo $skill['level']; ?>%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="?delete_skill=<?php echo $skill['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this skill?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No skills found. Add some skills to get started.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Login Form -->
            <div class="login-container">
                <div class="scanner-effect"></div>
                <div class="login-header">
                    <h1>Admin Login</h1>
                    <p>Enter your credentials to access the admin panel</p>
                </div>
                
                <?php if(!empty($login_err)): ?>
                    <div class="login-error">
                        <?php echo $login_err; ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="<?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <?php if(!empty($username_err)): ?>
                            <div class="invalid-feedback"><?php echo $username_err; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="<?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <?php if(!empty($password_err)): ?>
                            <div class="invalid-feedback"><?php echo $password_err; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="login_submit" value="1">
                    <button type="submit" class="btn">Login</button>
                </form>
                
                <a href="index.php" class="back-to-site">← Back to website</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if($is_logged_in): ?>
    <script>
        // Tab functionality
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                // Remove active class from all tabs and tab contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to current tab and tab content
                tab.classList.add('active');
                document.getElementById(tabId + '-tab').classList.add('active');
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>