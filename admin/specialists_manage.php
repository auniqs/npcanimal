<?php
require_once '../includes/config.php';

$pageTitle = 'Управление специалистами';
require_once '../includes/header.php';

// Загрузка данных
$specialists = json_decode(file_get_contents('../data/specialists.json'), true);

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $newSpecialist = [
            'id' => max(array_column($specialists, 'id')) + 1,
            'name' => $_POST['name'],
            'position' => $_POST['position'],
            'bio' => $_POST['bio'],
            'image' => $_POST['image']
        ];
        $specialists[] = $newSpecialist;
    } 
    elseif ($action === 'edit') {
        foreach ($specialists as &$spec) {
            if ($spec['id'] == $_POST['id']) {
                $spec['name'] = $_POST['name'];
                $spec['position'] = $_POST['position'];
                $spec['bio'] = $_POST['bio'];
                $spec['image'] = $_POST['image'];
                break;
            }
        }
    }
    elseif ($action === 'delete') {
        $specialists = array_filter($specialists, function($s) { 
            return $s['id'] != $_POST['id']; 
        });
    }

    // Сохраняем изменения
    file_put_contents('../data/specialists.json', json_encode(array_values($specialists), JSON_PRETTY_PRINT));
    header("Location: specialists_manage.php");
    exit;
}

// Форма редактирования/добавления
$editSpecialist = null;
if (isset($_GET['edit'])) {
    $editSpecialist = current(array_filter($specialists, function($s) { 
        return $s['id'] == $_GET['edit']; 
    }));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление специалистами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Управление специалистами</h2>
        
        <!-- Форма добавления/редактирования -->
        <div class="card mb-4">
            <div class="card-header">
                <?= $editSpecialist ? 'Редактирование' : 'Добавление' ?> специалиста
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editSpecialist ? 'edit' : 'add' ?>">
                    <?php if ($editSpecialist): ?>
                        <input type="hidden" name="id" value="<?= $editSpecialist['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">ФИО</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?= htmlspecialchars($editSpecialist['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Должность</label>
                        <input type="text" name="position" class="form-control" 
                               value="<?= htmlspecialchars($editSpecialist['position'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Биография</label>
                        <textarea name="bio" class="form-control" rows="3" required><?= 
                            htmlspecialchars($editSpecialist['bio'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Фото (путь к файлу)</label>
                        <input type="text" name="image" class="form-control" 
                               value="<?= htmlspecialchars($editSpecialist['image'] ?? 'images/specialists/default.jpg') ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <?php if ($editSpecialist): ?>
                        <a href="specialists_manage.php" class="btn btn-secondary">Отмена</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Список специалистов -->
        <div class="card">
            <div class="card-header">Список специалистов</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Фото</th>
                            <th>ФИО</th>
                            <th>Должность</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($specialists as $specialist): ?>
                        <tr>
                            <td><img src="../<?= htmlspecialchars($specialist['image']) ?>" width="50" height="50" class="rounded-circle"></td>
                            <td><?= htmlspecialchars($specialist['name']) ?></td>
                            <td><?= htmlspecialchars($specialist['position']) ?></td>
                            <td>
                                <a href="?edit=<?= $specialist['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $specialist['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Удалить специалиста?')">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>