<?php


session_start();

$pdo = new PDO('sqlite:todo.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        pwd TEXT NOT NULL
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS tasks (
        task_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        is_done INTEGER NOT NULL DEFAULT 0
    )
");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$currentName   = $_SESSION['name'] ?? 'User';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    if ($title !== '') {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, is_done) VALUES (?, ?, 0)");
        $stmt->execute([$currentUserId, $title]);
        header("Location: index.php");
        exit;

    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    if ($id > 0) {
        $stmt = $pdo->prepare("
            UPDATE tasks
               SET is_done = CASE is_done WHEN 1 THEN 0 ELSE 1 END
             WHERE task_id = ? AND user_id = ?
        ");
        $stmt->execute([$id, $currentUserId]);
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$id, $currentUserId]);
    }
}

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY task_id DESC");
$stmt->execute([$currentUserId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>My todo</title>
</head>
<body>
    <h1>My Todo App</h1>

    <div style="width:70%;margin:auto;display:flex;justify-content:space-between;align-items:center;">
        <div>Welcome, <strong><?php echo htmlspecialchars($currentName, ENT_QUOTES, 'UTF-8'); ?></strong></div>
        <div><a href="logout.php">Logout</a></div>
    </div>

    <div id="form-container">
        <form action="" method="post">
            <input type="text" name="title" id="title" placeholder="Task title..." required>
            <button type="submit">Add Task</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Index</th>
            <th>Task Title</th>
            <th>Status</th>
            <th>Done/Undone</th>
            <th>Delete</th>
        </tr>
        <?php if (!$rows): ?>
            <tr>
                <td>0</td>
                <td>No tasks</td>
                <td> - </td>
                <td> - </td>
                <td> - </td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $t): ?>
                <tr>
                    <td><?php echo (int)$t['task_id']; ?></td>
                    <td><?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo ((int)$t['is_done'] === 1) ? '[done]' : ''; ?></td>
                    <td><a href="?toggle=<?php echo (int)$t['task_id']; ?>" class="button">Toggle</a></td>
                    <td><a href="?delete=<?php echo (int)$t['task_id']; ?>" class="button" onclick="return confirm('Delete this task?');">Delete</a></td>

                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

</body>
</html>
