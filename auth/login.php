<?php

session_start();

require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {

        $message = "Please fill in all fields.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password FROM users WHERE username = ?"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                header("Location: ../posts/index.php");
                exit();

            } else {

                $message = "Invalid username or password.";
            }

        } else {

            $message = "Invalid username or password.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ApexPlanet Task 3</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container" style="max-width: 500px;">

    <h1>Account Login</h1>
    <p style="color: #64748b; font-size: 14px; margin-top: -10px; margin-bottom: 20px;">
        Sign in to manage your student posts and updates
    </p>

    <?php if (!empty($message)): ?>
        <div class="alert alert-error">
            ⚠️ <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label for="username">Username</label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                placeholder="Enter your username"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter your password"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 6px;">Sign In</button>

    </form>

    <hr>

    <p style="text-align: center; font-size: 14px; margin-bottom: 0;">
        Don't have an account?
        <a href="register.php"><u>Register here</u></a>
    </p>

</div>

</body>
</html>