<?php
session_start();
require 'config.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if user already exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username already exists";
    } else {

        // Hash password
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash);

        if ($stmt->execute()) {
            $success = "Account created! You can now log in.";
        } else {
            $error = "Error creating account";
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('5CS045 background.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.15);
            padding: 40px;
            width: 350px;
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            color: white;
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: none;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            background: #4CAF50;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }

        a {
            color: #fff;
            text-decoration: underline;
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        .error {
            color: #ff8080;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .success {
            color: #90EE90;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>

</head>
<body>

<div class="register-container">

<h2>Create Account</h2>

<?php if (!empty($error)): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<?php if (!empty($success)): ?>
<p class="success"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST">

    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <!-- ❤️ CAPTCHA BLOCK ADDED EXACTLY AS REQUESTED -->
    <div class="mb-3 border p-3 rounded bg-light">
        <label class="form-label">Security Check: Enter the code below</label>
        <img src="captcha.php" alt="CAPTCHA Image" style="border: 2px solid #555; margin-bottom: 10px; display: block;">
        <input type="text" class="form-control" id="captcha_code" name="captcha_code" placeholder="Enter CAPTCHA Code" required autocomplete="off">
    </div>
    <!-- END CAPTCHA -->

    <button type="submit">Register</button>
</form>

<a href="login.php">Back to Login</a>

</div>

</body>
</html>
