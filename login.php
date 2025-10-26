<?php


session_start();

$pdo = new PDO('sqlite:todo.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        pwd TEXT NOT NULL,
        profile_pic TEXT
    )
");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '')   { $errors[] = "Email is required."; }
    if ($password === '') { $errors[] = "Password is required."; }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT user_id, name, email, pwd FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['pwd'])) {
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['name']    = $user['name'];

            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    <h1>Log in</h1>

    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="msg error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" novalidate>
            <label>
                Email:
                <input type="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>">
            </label>

            <label>
                Password:
                <input type="password" name="password" required>
            </label>

            <button type="submit">Log in</button>
        </form>

        <p>New here? <a href="register.php">Create an account</a>.</p>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>
