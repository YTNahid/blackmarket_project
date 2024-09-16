<?php 

include 'connect.php';


// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

// Login
if (isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Prepare and execute query
    $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role']; // Make sure to set the role
            $_SESSION['username'] = $row['username']; // Optional: store the username

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Incorrect Email or Password";
        }
    } else {
        echo "Not Found, Incorrect Email or Password";
    }

    // Close prepared statements
    $sql->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="login">
    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login" name="login">
            </div>
        </form>
        <a href="./register.php" style="margin-top: 15px;">Or Register</a>
        <a href="index.php" style="margin-top: 15px;"><button>Go Home</button></a>
    </div>
</body>
</html>
