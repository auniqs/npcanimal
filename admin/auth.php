<?php
require_once '../includes/config.php';

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: auth.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);
    
    $valid_username = 'auniqs';
    $valid_password = 'qwedsaq123';
    
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        header("Location: index.php");
        exit();
    } else {
        $error = 'Неверное имя пользователя или пароль';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация | Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="h5 mb-0">Админ-панель</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Имя пользователя</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Войти</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>