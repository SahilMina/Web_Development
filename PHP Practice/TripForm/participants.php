<?php
// Set connection variables
$server = "localhost";
$username = "";
$password = "";

// Create a database connection
$con = mysqli_connect($server, $username, $password);

// Check for connection success
if(!$con){
    die("connection to this database failed due to" . mysqli_connect_error());
}

// Fetch participants data
$sql = "SELECT * FROM `moviemate_Submission`.`Submission_form`";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants List</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Sriracha&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .participants-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .participant-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .participant-card:hover {
            transform: translateY(-5px);
        }

        .participant-card h3 {
            color: #4CAF50;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .participant-info {
            margin-bottom: 10px;
            color: #666;
        }

        .participant-info strong {
            color: #333;
        }

        .back-btn {
            display: inline-block;
            margin: 20px;
            text-decoration: none;
        }

        .budget-badge {
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .participants-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="btn back-btn">← Back to Form</a>
    <div class="participants-container">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="participant-card">';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '<div class="participant-info"><strong>Gender:</strong> ' . htmlspecialchars($row['gender']) . '</div>';
                echo '<div class="participant-info"><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</div>';
                echo '<div class="participant-info"><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</div>';
                echo '<div class="budget-badge">Budget: ₹' . htmlspecialchars($row['Budget']) . '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="participant-card"><h3>No participants yet!</h3></div>';
        }
        ?>
    </div>
</body>
</html> 
