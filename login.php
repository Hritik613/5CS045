<?php
session_start();
require 'config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    
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

        .login-container {
            background: rgba(255, 255, 255, 0.15);
            padding: 40px;
            width: 350px;
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            color: white;
        }

        .login-container h2 {
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
    </style>
</head>

<body>

<div class="login-container">

<h2>Login</h2>

<?php if (!empty($error)): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>

    <a href="register.php">Don't have an account? Register here</a>
</form>

</div>

</body>
</html>
