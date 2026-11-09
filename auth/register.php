<?php

session_start();

require_once "../config/database.php";

$message = "";
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {

        // Check if username already exists
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Username already exists. Please choose a different one.";

        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (username, password) VALUES (?, ?)"
            );

            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                $message = "Registration successful! You can now sign in.";
                $isSuccess = true;
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ApexPlanet Task 3</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container" style="max-width: 500px;">

    <h1>Create Account</h1>
    <p style="color: #64748b; font-size: 14px; margin-top: -10px; margin-bottom: 20px;">
        Register a new account to start posting and managing records
    </p>

    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $isSuccess ? 'alert-success' : 'alert-error'; ?>">
            <?php echo $isSuccess ? '✅' : '⚠️'; ?> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label for="username">Username</label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                placeholder="Choose a username"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Choose a strong password"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 6px;">Register</button>

    </form>

    <hr>

    <p style="text-align: center; font-size: 14px; margin-bottom: 0;">
        Already have an account?
        <a href="login.php"><u>Login here</u></a>
    </p>

</div>

</body>
</html>