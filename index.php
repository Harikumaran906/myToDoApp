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



$stmt = $pdo->prepare("SELECT user_id, name, email, profile_pic FROM users WHERE user_id = ?");
$stmt->execute([$currentUserId]);
$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

$currentEmail = $userRow['email'] ?? '';
$currentPic   = $userRow['profile_pic'] ?: 'uploads/default_pic.png';

$notice = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_name') {
        $newName = trim($_POST['new_name'] ?? '');
        if ($newName === '') {
            $errors[] = 'Name cannot be empty.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE user_id = ?");
            $stmt->execute([$newName, $currentUserId]);
            $_SESSION['name'] = $newName;
            $currentName = $newName;
            $notice = 'Name updated.';
        }
    }

    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_title'], $_POST['task_id'])) {
    $newTitle = trim($_POST['edit_title']);
    $taskId   = (int)$_POST['task_id'];

    if ($newTitle !== '' && $taskId > 0) {
        $isDone = isset($_POST['is_done']) ? (int)$_POST['is_done'] : 0;
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, is_done = ? WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$newTitle, $isDone, $taskId, $currentUserId]);
        header("Location: index.php");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $title = trim($_POST['title'] ?? '');
    if ($title !== '') {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, is_done) VALUES (?, ?, 0)");
        $stmt->execute([$currentUserId, $title]);
        header("Location: index.php");
        exit;
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
        <title>My Todo App</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <header>
            <h1>My Todos</h1>
            <div id="prof-icon" style="background-image:url('<?php echo htmlspecialchars($currentPic, ENT_QUOTES, 'UTF-8'); ?>');"></div>
        </header>
        

        <div id="dashboard">
            <div id="profile-btn" class="dashBtn">Profile</div>
            <div id="logoutBtn" class="dashBtn">Logout</div>
        </div>

        <div class="topbar">
            <div>Welcome, <strong><?php echo htmlspecialchars($currentName, ENT_QUOTES, 'UTF-8'); ?></strong></div>
            
        </div>

        

        

        <?php if ($notice || !empty($errors)): ?>
        <div class="flash">
            <?php if ($notice): ?>
                <div class="msg success"><?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="msg error">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="overlay" id="profile-overlay" aria-hidden="true">
            <div class="id-card">
                <button class="id-close" id="close-card">×</button>

                <div class="id-left" style="background-image:url('<?php echo htmlspecialchars($currentPic, ENT_QUOTES, 'UTF-8'); ?>');"></div>
                <div class="vert-line"></div>

                <div class="id-right">
                    <div class="id-row">
                        <div class="id-label">Name:</div>
                        <div class="id-value" id="val-name"><?php echo htmlspecialchars($currentName, ENT_QUOTES, 'UTF-8'); ?></div>
                        <button class="edit-btn" id="edit-name">Edit</button>
                    </div>
                    <div class="id-row">
                        <div class="id-label">E-mail:</div>
                        <div class="id-value" id="val-email"><?php echo htmlspecialchars($currentEmail, ENT_QUOTES, 'UTF-8'); ?></div>
                        
                    </div>
                </div>

                <div class="mini-popup" id="popup-name">
                    <h3>Edit name</h3>
                    <form action="" method="post">
                        <input type="hidden" name="action" value="update_name">
                        <input type="text" name="new_name" required
                            value="<?php echo htmlspecialchars($currentName, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="mini-actions">
                            <button type="submit">Save</button>
                        </div>
                    </form>
                </div>

                
            </div>
        </div>

        <div id="form-container" class="overlay" aria-hidden="true">
            <div class="addTaskCard">
                <form action="" method="post">
                    <input type="text" name="title" id="title" placeholder="Task title..." required>
                    <button type="submit">Add Task</button>
                </form>
            </div>
            
        </div>

        <div id="add-task">Add Task</div>


        <div class="overlay" id="edit-overlay">
            <div id="edit-popup">
                <h3>Edit Task</h3>
                <form id="edit-form" action="" method="post">
                    <input type="hidden" name="task_id" id="edit_task_id">
                    <input type="hidden" name="is_done" id="edit_is_done" value="0">

                    <input type="text" name="edit_title" id="edit_title" required>
                    <div id="done-box-container">
                        <label>Done:</label>
                        <div id="done-box"></div>
                    </div>

                    
                    <button type="submit">Save</button>
                    <button type="button" id="cancel-edit">Cancel</button>
                </form>
              
            </div>
        </div>

        <table id="todo-table">
            <tr>
                <th>Task Title</th>
                <th>Done/Undone</th>
                <th>Delete</th>
            </tr>
            <?php if (!$rows): ?>
                <tr>
                    <td>No tasks</td>
                    <td> - </td>
                    <td> - </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $t): ?>
                    <tr>
                        <td class="<?php echo ((int)$t['is_done'] === 1) ? 'done-task' : ''; ?>">
                            <?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="button edit-task"
                                data-id="<?php echo (int)$t['task_id']; ?>"
                                data-title="<?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                Edit
                            </button>
                        </td>
                        <td><a href="?delete=<?php echo (int)$t['task_id']; ?>" class="button" onclick="return confirm('Delete this task?');">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>

        <script src="script.js"></script>
    </body>
</html>
