<?php
require_once '../includes/config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$pageTitle = 'Управление публикациями';
require_once '../includes/header.php';

// Обработка действий
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $pubId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // Удаление публикации
    if ($action == 'delete' && $pubId > 0) {
        try {
            // Получаем путь к файлу для удаления
            $stmt = $pdo->prepare("SELECT file_path FROM publications WHERE id = ?");
            $stmt->execute([$pubId]);
            $pub = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Удаляем запись из БД
            $stmt = $pdo->prepare("DELETE FROM publications WHERE id = ?");
            $stmt->execute([$pubId]);
            
            // Удаляем файл, если он есть
            if (!empty($pub['file_path']) && file_exists('../'.$pub['file_path'])) {
                unlink('../'.$pub['file_path']);
            }
            
            $_SESSION['success_message'] = 'Публикация успешно удалена';
            header("Location: publications_manage.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error_message'] = 'Ошибка при удалении публикации: ' . $e->getMessage();
            header("Location: publications_manage.php");
            exit();
        }
    }
    
    // Форма редактирования/добавления
    if ($action == 'edit' || $action == 'add') {
        $pub = [
            'id' => 0,
            'title' => '',
            'author' => '',
            'year' => date('Y'),
            'description' => '',
            'file_path' => '',
            'category' => 'articles'
        ];
        
        if ($action == 'edit' && $pubId > 0) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM publications WHERE id = ?");
                $stmt->execute([$pubId]);
                $pub = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$pub) {
                    $_SESSION['error_message'] = 'Публикация не найдена';
                    header("Location: publications_manage.php");
                    exit();
                }
            } catch(PDOException $e) {
                $_SESSION['error_message'] = 'Ошибка при загрузке публикации: ' . $e->getMessage();
                header("Location: publications_manage.php");
                exit();
            }
        }
        
        // Обработка формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = sanitize($_POST['title']);
            $author = sanitize($_POST['author']);
            $year = intval($_POST['year']);
            $description = sanitize($_POST['description']);
            $category = sanitize($_POST['category']);
            $currentFile = sanitize($_POST['current_file']);
            
            // Валидация
            $errors = [];
            
            if (empty($title)) {
                $errors['title'] = 'Название обязательно';
            }
            
            if (empty($author)) {
                $errors['author'] = 'Автор обязателен';
            }
            
            if ($year < 1900 || $year > date('Y') + 1) {
                $errors['year'] = 'Некорректный год';
            }
            
            // Обработка загрузки файла
            $filePath = $currentFile;
            
            if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/publications/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
                $targetPath = $uploadDir . $fileName;
                
                // Проверка типа файла
                $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $fileType = $_FILES['file']['type'];
                
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                        // Удаляем старый файл, если он есть
                        if (!empty($currentFile) && file_exists('../'.$currentFile)) {
                            unlink('../'.$currentFile);
                        }
                        
                        $filePath = str_replace('../', '', $targetPath);
                    } else {
                        $errors['file'] = 'Ошибка при загрузке файла';
                    }
                } else {
                    $errors['file'] = 'Недопустимый тип файла (разрешены PDF, DOC, DOCX)';
                }
            }
            
            if (empty($errors)) {
                try {
                    if ($action == 'add') {
                        $stmt = $pdo->prepare("INSERT INTO publications (title, author, year, description, file_path, category) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $author, $year, $description, $filePath, $category]);
                        $message = 'Публикация успешно добавлена';
                    } else {
                        $stmt = $pdo->prepare("UPDATE publications SET title = ?, author = ?, year = ?, description = ?, file_path = ?, category = ? WHERE id = ?");
                        $stmt->execute([$title, $author, $year, $description, $filePath, $category, $pubId]);
                        $message = 'Публикация успешно обновлена';
                    }
                    
                    $_SESSION['success_message'] = $message;
                    header("Location: publications_manage.php");
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
                <h2><?php echo $action == 'add' ? 'Добавить публикацию' : 'Редактировать публикацию'; ?></h2>
                <a href="publications_manage.php" class="btn btn-outline-secondary">Назад</a>
            </div>
            
            <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Название <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($pub['title']); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="author" class="form-label">Автор(ы) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['author']) ? 'is-invalid' : ''; ?>" id="author" name="author" value="<?php echo htmlspecialchars($pub['author']); ?>" required>
                    <?php if (isset($errors['author'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['author']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="year" class="form-label">Год издания <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?php echo isset($errors['year']) ? 'is-invalid' : ''; ?>" id="year" name="year" value="<?php echo htmlspecialchars($pub['year']); ?>" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                        <?php if (isset($errors['year'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['year']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="articles" <?php echo $pub['category'] == 'articles' ? 'selected' : ''; ?>>Научные статьи</option>
                            <option value="books" <?php echo $pub['category'] == 'books' ? 'selected' : ''; ?>>Монографии</option>
                            <option value="reports" <?php echo $pub['category'] == 'reports' ? 'selected' : ''; ?>>Отчеты</option>
                            <option value="conferences" <?php echo $pub['category'] == 'conferences' ? 'selected' : ''; ?>>Материалы конференций</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($pub['description']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="file" class="form-label">Файл (PDF, DOC, DOCX)</label>
                    <input type="file" class="form-control <?php echo isset($errors['file']) ? 'is-invalid' : ''; ?>" id="file" name="file">
                    <?php if (isset($errors['file'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['file']; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($pub['file_path'])): ?>
                    <div class="mt-2">
                        <a href="../<?php echo $pub['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">Просмотреть файл</a>
                        <input type="hidden" name="current_file" value="<?php echo $pub['file_path']; ?>">
                        <div class="form-check d-inline-block">
                            <input class="form-check-input" type="checkbox" id="delete_file" name="delete_file">
                            <label class="form-check-label" for="delete_file">Удалить файл</label>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="current_file" value="">
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="publications_manage.php" class="btn btn-outline-secondary">Отмена</a>
            </form>
        </div>
        <?php
        require_once '../includes/footer.php';
        exit();
    }
}

// Отображение списка публикаций
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Управление публикациями</h2>
        <a href="publications_manage.php?action=add" class="btn btn-primary">Добавить публикацию</a>
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
                            <th>Название</th>
                            <th>Автор</th>
                            <th>Год</th>
                            <th>Категория</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM publications ORDER BY year DESC, title ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>
                                        <td>'.$row['id'].'</td>
                                        <td>'.substr($row['title'], 0, 40).'</td>
                                        <td>'.substr($row['author'], 0, 20).'</td>
                                        <td>'.$row['year'].'</td>
                                        <td>'.ucfirst($row['category']).'</td>
                                        <td>
                                            <a href="publications_manage.php?action=edit&id='.$row['id'].'" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                            <a href="publications_manage.php?action=delete&id='.$row['id'].'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Вы уверены?\')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>';
                            }
                        } catch(PDOException $e) {
                            echo '<tr><td colspan="6" class="text-center text-danger">Ошибка загрузки публикаций: '.$e->getMessage().'</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>