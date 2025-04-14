<?php
require_once "config.php";

// Simple admin access
$is_admin = false;
if (isset($_GET['admin']) && $_GET['admin'] === 'true') {
    $_SESSION['admin_access'] = true;
    header("Location: admin.php");
    exit;
}
if (isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === true) {
    $is_admin = true;
}

// Fetch skills from database
$skills_result = false;
$skills_query = "SELECT * FROM skills ORDER BY category, level DESC";
$skills_result = mysqli_query($conn, $skills_query);

// Fetch gallery items - use the correct field name
// gallery table fields: id, title, description, image_path, visible, upload_date
$gallery_result = false;
$gallery_query = "SELECT * FROM gallery WHERE visible = 1 ORDER BY upload_date DESC";
$gallery_result = mysqli_query($conn, $gallery_query);

// Website information - update this with your details
$site_info = [
    'name' => 'All About Sahil',
    'title' => 'Developer & Digital Creator',
    'about' => 'Creative developer with a passion for building innovative digital experiences. Specializing in web development and interactive media.',
    'email' => 'your.email@example.com',
    'github' => 'https://github.com/yourusername',
    'linkedin' => 'https://linkedin.com/in/yourusername'
];

// Convert percentage to skill level
function getSkillLevel($percentage) {
    if ($percentage < 25) {
        return ['level' => 'noob', 'label' => 'Noob'];
    } else if ($percentage < 50) {
        return ['level' => 'beginner', 'label' => 'Beginner'];
    } else if ($percentage < 75) {
        return ['level' => 'intermediate', 'label' => 'Intermediate'];
    } else {
        return ['level' => 'advanced', 'label' => 'Advanced'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_info['name']; ?> - Portfolio</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <div class="profile">
                <h1><?php echo $site_info['name']; ?></h1>
                <p class="title"><?php echo $site_info['title']; ?></p>
            </div>
            <nav>
                <ul>
                    <li><a href="#skills">Skills</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <?php if($is_admin): ?>
                    <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <main>
            <section id="about">
                <div class="section-content">
                    <h2>About Me</h2>
                    <p><?php echo $site_info['about']; ?></p>
                </div>
            </section>

            <section id="skills">
                <div class="section-content">
                    <h2>My Skills</h2>
                    <div class="skills-container">
                        <?php
                        if($skills_result && mysqli_num_rows($skills_result) > 0) {
                            $current_category = "";
                            
                            while($skill = mysqli_fetch_assoc($skills_result)) {
                                if($current_category != $skill['category']) {
                                    if($current_category != "") {
                                        echo "</div>";
                                    }
                                    
                                    $current_category = $skill['category'];
                                    echo '<div class="skill-category">';
                                    echo '<h3>' . htmlspecialchars($skill['category']) . '</h3>';
                                }
                                
                                $skillLevel = getSkillLevel($skill['level']);
                                
                                echo '<div class="skill">';
                                echo '<div class="skill-info">';
                                echo '<span class="skill-name">' . htmlspecialchars($skill['name']) . '</span>';
                                echo '<span class="skill-level ' . $skillLevel['level'] . '">' . $skillLevel['label'] . '</span>';
                                echo '</div>';
                                echo '<div class="progress-bar">';
                                echo '<div class="progress" style="width: ' . $skill['level'] . '%"></div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            if($current_category != "") {
                                echo "</div>";
                            }
                        } else {
                            // Sample skills if database is empty
                            $sample_skills = [
                                ['Web Development', 85, 'Technical'],
                                ['UI/UX Design', 75, 'Technical'],
                                ['JavaScript', 60, 'Programming'],
                                ['PHP', 70, 'Programming'],
                                ['Photoshop', 40, 'Design'],
                                ['3D Modeling', 25, 'Design'],
                                ['Game Development', 15, 'Technical']
                            ];
                            
                            $current_category = "";
                            foreach($sample_skills as $skill) {
                                if($current_category != $skill[2]) {
                                    if($current_category != "") {
                                        echo "</div>";
                                    }
                                    
                                    $current_category = $skill[2];
                                    echo '<div class="skill-category">';
                                    echo '<h3>' . htmlspecialchars($current_category) . '</h3>';
                                }
                                
                                $skillLevel = getSkillLevel($skill[1]);
                                
                                echo '<div class="skill">';
                                echo '<div class="skill-info">';
                                echo '<span class="skill-name">' . htmlspecialchars($skill[0]) . '</span>';
                                echo '<span class="skill-level ' . $skillLevel['level'] . '">' . $skillLevel['label'] . '</span>';
                                echo '</div>';
                                echo '<div class="progress-bar">';
                                echo '<div class="progress" style="width: ' . $skill[1] . '%"></div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            if($current_category != "") {
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </section>

            <section id="gallery">
                <div class="section-content">
                    <h2>My Gallery</h2>
                    <div class="gallery-grid">
                        <?php
                        if($gallery_result && mysqli_num_rows($gallery_result) > 0) {
                            while($item = mysqli_fetch_assoc($gallery_result)) {
                                echo '<div class="gallery-item">';
                                echo '<img src="' . htmlspecialchars($item['image_path']) . '" alt="' . htmlspecialchars($item['title']) . '">';
                                echo '<div class="gallery-item-details">';
                                echo '<h3>' . htmlspecialchars($item['title']) . '</h3>';
                                echo '<p>' . htmlspecialchars($item['description']) . '</p>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            // Sample gallery items
                            $placeholders = [
                                ['Project Alpha', 'Sci-fi interface concept with futuristic elements and holographic displays'],
                                ['Neural Network Visualization', 'A visual representation of AI learning patterns and connections'],
                                ['Digital Ecosystem', 'Interactive environment showing digital data flow in a cybernetic simulation'],
                                ['Quantum Interface', 'User interface designed for quantum computing applications'],
                                ['Cyber Security Dashboard', 'Real-time visualization of network traffic and security protocols'],
                                ['Virtual Reality Space', 'Immersive VR environment designed for scientific exploration']
                            ];
                            
                            for($i = 0; $i < count($placeholders); $i++) {
                                echo '<div class="gallery-item">';
                                echo '<img src="https://source.unsplash.com/random/600x400?tech,' . ($i+1) . '" alt="' . $placeholders[$i][0] . '">';
                                echo '<div class="gallery-item-details">';
                                echo '<h3>' . $placeholders[$i][0] . '</h3>';
                                echo '<p>' . $placeholders[$i][1] . '</p>';
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </section>

            <section id="contact">
                <div class="section-content">
                    <h2>Contact Me</h2>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo $site_info['email']; ?>"><?php echo $site_info['email']; ?></a>
                        </div>
                        <div class="contact-item">
                            <i class="fab fa-github"></i>
                            <a href="<?php echo $site_info['github']; ?>" target="_blank">GitHub</a>
                        </div>
                        <div class="contact-item">
                            <i class="fab fa-linkedin"></i>
                            <a href="<?php echo $site_info['linkedin']; ?>" target="_blank">LinkedIn</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo $site_info['name']; ?>. All rights reserved.</p>
        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html> 