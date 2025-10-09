<?php

$pdo = new PDO('sqlite:todo.db');
$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        pwd TEXT NOT NULL
    )
");

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']  ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password']   ?? '';
    $confirm  = $_POST['confirm']    ?? '';

    if ($name === '')    { $errors[] = "Name is required."; }
    if ($email === '')   { $errors[] = "Email is required."; }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email format looks invalid.";
    }
    if ($password === '') { $errors[] = "Password is required."; }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, pwd) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);

            $success = "Registration successful. You can now <a href=\"login.php\">log in</a>.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = "That email is already registered.";
            } else {
                $errors[] = "Database error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Register</title>
</head>
<body>
    
    <h1>Register</h1>

    <div class="container">

        <?php if(!empty($errors)): ?>
            <div class="msg error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif?>

        <?php if ($success): ?>
            <div class="msg success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" novalidate>
            <label>
                Name:
                <input type="text" name="name" required value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : ''; ?>">
            </label>

            <label>
                Email:
                <input type="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>">
            </label>

            <label>
                Password:
                <input type="password" name="password" required>
            </label>

            <label>
                Confirm Password:
                <input type="password" name="confirm" required>
            </label>

            <button type="submit">Create Account</button>
        </form>

        <p>Already registered? <a href="login.php">Log in</a>.</p>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>