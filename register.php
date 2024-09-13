<?php 
include 'connect.php';

// Start the session
session_start();

// Initialize message variables
$error = "";
$success = "";

// Registration
if (isset($_POST['register'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $contact = htmlspecialchars($_POST['contact']);
    $gender = htmlspecialchars($_POST['gender']);
    $birth_date = htmlspecialchars($_POST['birth_date']);

    // Check if email or username already exists
    $checkUser = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $checkUser->bind_param("ss", $email, $username);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['email'] === $email) {
                $error = "Email Address Already Exists!";
            }
            if ($row['username'] === $username) {
                $error = "Username Already Exists!";
            }
        }
    } else {
        // Insert new user into the database
        $insertQuery = $conn->prepare("INSERT INTO users (username, email, password, contact, gender, birth_date) VALUES (?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("ssssss", $username, $email, $password_hash, $contact, $gender, $birth_date);

        if ($insertQuery->execute()) {
            $success = "Registration successful. Redirecting to login...";
        } else {
            $error = "Error: " . $insertQuery->error;
        }
    }

    // Close prepared statements
    $checkUser->close();
    if (isset($insertQuery)) {
        $insertQuery->close();
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="register">
    <div class="container">
        <h2>Registration Form</h2>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact Number:</label>
                <input type="text" id="contact" name="contact" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="birth">Date of Birth:</label>
                <input type="date" id="birth_date" name="birth_date" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Register" name="register">
            </div>
        </form>
        <a href="./login.php" style="margin-top: 15px;">Or Login</a>
        <a href="index.php" style="margin-top: 15px;"><button>Go Home</button></a>
    </div>

    <!-- Show Alerts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Display alert if there is an error message
            <?php if ($error): ?>
                alert("<?php echo htmlspecialchars($error); ?>");
            <?php endif; ?>

            // Display alert if there is a success message
            <?php if ($success): ?>
                alert("<?php echo htmlspecialchars($success); ?>");
                window.location.href = 'login.php';
            <?php endif; ?>
        });
    </script>
</body>
</html>
