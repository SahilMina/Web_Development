<?php
$insert = false;
if(isset($_POST['name'])){
    $server = "localhost";
    $username = "";
    $password = "";
    $con = mysqli_connect($server, $username, $password);

    if(!$con){
        die("connection to this database failed due to" . mysqli_connect_error());
    }
    
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $budget = $_POST['budget'];
    $sql = "INSERT INTO `moviemate_Submission`.`Submission_form` (`name`, `gender`, `email`, `phone`, `Budget`) VALUES ('$name', '$gender', '$email','$phone', '$budget');";

    if($con->query($sql) == true){       
        $insert = true;
    }
    else{
        echo "ERROR: $sql <br> $con->error";
    }
  
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Travel Form</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Sriracha&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Coorg Trip form</h1>
        <p>Enter your details and submit this form to confirm your participation in the trip</p>
        <?php
        if($insert == true){
            echo "<p class='submitMsg'>Thanks for submitting your form.Let's explore Coorg Together</p>";
        }
        ?>
        <form action="index.php" method="post">
            <input type="text" name="name" id="name" placeholder="Enter your name" required>
            <div class="gender-options">
                <div class="gender-option">
                    <input type="radio" name="gender" id="male" value="Male" required>
                    <label for="male">Male</label>
                </div>
                <div class="gender-option">
                    <input type="radio" name="gender" id="female" value="Female">
                    <label for="female">Female</label>
                </div>
                <div class="gender-option">
                    <input type="radio" name="gender" id="other" value="Other">
                    <label for="other">Other</label>
                </div>
            </div>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <input type="tel" name="phone" id="phone" placeholder="Enter your phone" required>
            <div class="budget-container">
                <label for="budget">Select your budget (₹): <span id="budget-value">3000</span></label>
                <input type="range" name="budget" id="budget" min="1000" max="5000" step="500" value="3000" required>
            </div>
            <div><button class="btn">Submit</button></div><br>
        </form>
        <div style="text-align: center;"><button class="btn"><a href="participants.php" style="text-decoration: none; color: white;">Show Participants</a></button></div>
    </div>
    <script>
        const budgetSlider = document.getElementById('budget');
        const budgetValue = document.getElementById('budget-value');
        
        budgetSlider.addEventListener('input', function() {
            budgetValue.textContent = this.value;
        });
    </script>
</body>
</html>
