<?php
require_once '../includes/config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$pageTitle = 'Управление новостями';
require_once '../includes/header.php';

// Обработка действий
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $newsId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // Удаление новости
    if ($action == 'delete' && $newsId > 0) {
        try {
            // Получаем путь к изображению для удаления
            $stmt = $pdo->prepare("SELECT image_path FROM news WHERE id = ?");
            $stmt->execute([$newsId]);
            $news = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Удаляем запись из БД
            $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute([$newsId]);
            
            // Удаляем изображение, если оно есть
            if (!empty($news['image_path']) && file_exists('../'.$news['image_path'])) {
                unlink('../'.$news['image_path']);
            }
            
            $_SESSION['success_message'] = 'Новость успешно удалена';
            header("Location: news_manage.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error_message'] = 'Ошибка при удалении новости: ' . $e->getMessage();
            header("Location: news_manage.php");
            exit();
        }
    }
    
    // Форма редактирования/добавления
    if ($action == 'edit' || $action == 'add') {
        $news = [
            'id' => 0,
            'title' => '',
            'content' => '',
            'image_path' => '',
            'author' => $_SESSION['username']
        ];
        
        if ($action == 'edit' && $newsId > 0) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
                $stmt->execute([$newsId]);
                $news = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$news) {
                    $_SESSION['error_message'] = 'Новость не найдена';
                    header("Location: news_manage.php");
                    exit();
                }
            } catch(PDOException $e) {
                $_SESSION['error_message'] = 'Ошибка при загрузке новости: ' . $e->getMessage();
                header("Location: news_manage.php");
                exit();
            }
        }
        
        // Обработка формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = sanitize($_POST['title']);
            $content = sanitize($_POST['content']);
            $author = sanitize($_POST['author']);
            $currentImage = sanitize($_POST['current_image']);
            
            // Валидация
            $errors = [];
            
            if (empty($title)) {
                $errors['title'] = 'Заголовок обязателен';
            }
            
            if (empty($content)) {
                $errors['content'] = 'Содержание обязательно';
            }
            
            if (empty($author)) {
                $errors['author'] = 'Автор обязателен';
            }
            
            // Обработка загрузки изображения
            $imagePath = $currentImage;
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/news/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                
                // Проверка типа файла
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = $_FILES['image']['type'];
                
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        // Удаляем старое изображение, если оно есть
                        if (!empty($currentImage) && file_exists('../'.$currentImage)) {
                            unlink('../'.$currentImage);
                        }
                        
                        $imagePath = str_replace('../', '', $targetPath);
                    } else {
                        $errors['image'] = 'Ошибка при загрузке изображения';
                    }
                } else {
                    $errors['image'] = 'Недопустимый тип файла';
                }
            }
            
            if (empty($errors)) {
                try {
                    if ($action == 'add') {
                        $stmt = $pdo->prepare("INSERT INTO news (title, content, image_path, author) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$title, $content, $imagePath, $author]);
                        $message = 'Новость успешно добавлена';
                    } else {
                        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image_path = ?, author = ? WHERE id = ?");
                        $stmt->execute([$title, $content, $imagePath, $author, $newsId]);
                        $message = 'Новость успешно обновлена';
                    }
                    
                    $_SESSION['success_message'] = $message;
                    header("Location: news_manage.php");
                    exit();
                } catch(PDOException $e) {
                    $errors['db'] = 'Ошибка базы данных: ' . $e->getMessage();
                }
            }
        }
        
        // Отображение формы
        ?>
        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $action == 'add' ? 'Добавить новость' : 'Редактировать новость'; ?></h2>
                <a href="news_manage.php" class="btn btn-outline-secondary">Назад</a>
            </div>
            
            <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Заголовок <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Содержание <span class="text-danger">*</span></label>
                    <textarea class="form-control <?php echo isset($errors['content']) ? 'is-invalid' : ''; ?>" id="content" name="content" rows="10" required><?php echo htmlspecialchars($news['content']); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['content']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="author" class="form-label">Автор <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['author']) ? 'is-invalid' : ''; ?>" id="author" name="author" value="<?php echo htmlspecialchars($news['author']); ?>" required>
                    <?php if (isset($errors['author'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['author']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Изображение</label>
                    <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image">
                    <?php if (isset($errors['image'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['image']; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($news['image_path'])): ?>
                    <div class="mt-2">
                        <img src="../<?php echo $news['image_path']; ?>" class="img-thumbnail" style="max-height: 150px;">
                        <input type="hidden" name="current_image" value="<?php echo $news['image_path']; ?>">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image">
                            <label class="form-check-label" for="delete_image">Удалить изображение</label>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="current_image" value="">
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="news_manage.php" class="btn btn-outline-secondary">Отмена</a>
            </form>
        </div>
        <?php
        require_once '../includes/footer.php';
        exit();
    }
}

// Отображение списка новостей
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Управление новостями</h2>
        <a href="news_manage.php?action=add" class="btn btn-primary">Добавить новость</a>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Заголовок</th>
                            <th>Дата</th>
                            <th>Автор</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM news ORDER BY publication_date DESC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>
                                        <td>'.$row['id'].'</td>
                                        <td>'.substr($row['title'], 0, 50).'</td>
                                        <td>'.date('d.m.Y', strtotime($row['publication_date'])).'</td>
                                        <td>'.$row['author'].'</td>
                                        <td>
                                            <a href="news_manage.php?action=edit&id='.$row['id'].'" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                            <a href="news_manage.php?action=delete&id='.$row['id'].'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Вы уверены?\')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>';
                            }
                        } catch(PDOException $e) {
                            echo '<tr><td colspan="5" class="text-center text-danger">Ошибка загрузки новостей: '.$e->getMessage().'</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>