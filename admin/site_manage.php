<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Загрузка данных
$header = json_decode(file_get_contents('../data/header.json'), true);
$footer = json_decode(file_get_contents('../data/footer.json'), true);
$achievements = json_decode(file_get_contents('../data/achievements.json'), true);
$departments = json_decode(file_get_contents('../data/departments.json'), true);

// Обработка форм
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_header'])) {
        $header = [
            'logo' => $_POST['logo'],
            'menu' => array_map(function($item) {
                return ['title' => $item['title'], 'url' => $item['url']];
            }, $_POST['menu']),
        ];
        file_put_contents('../data/header.json', json_encode($header, JSON_PRETTY_PRINT));
    }
    
    if (isset($_POST['save_footer'])) {
        $footer = [
            'copyright' => $_POST['copyright'],
            'contacts' => [
                'address' => $_POST['address'],
                'phone' => $_POST['phone']
            ],
            'social' => array_map(function($item) {
                return ['icon' => $item['icon'], 'url' => $item['url']];
            }, $_POST['social'])
        ];
        file_put_contents('../data/footer.json', json_encode($footer, JSON_PRETTY_PRINT));
    }
    
    if (isset($_POST['save_achievements'])) {
        $achievements = array_map(function($item) {
            return ['value' => $item['value'], 'label' => $item['label']];
        }, $_POST['achievements']);
        file_put_contents('../data/achievements.json', json_encode($achievements, JSON_PRETTY_PRINT));
    }

    if (isset($_POST['save_departments'])) {
    $departments = array_map(function($item) {
        return [
            'title' => $item['title'],
            'head' => $item['head'],
            'phone' => $item['phone'],
            'location' => $item['location']];
        }, $_POST['departments']);
        file_put_contents('../data/departments.json', json_encode($departments, JSON_PRETTY_PRINT));
    }
    
    header("Location: site_manage.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление сайтом</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <h2>Управление контентом сайта</h2>
        
        <!-- Навигация между разделами -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#header">Хэдер</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#footer">Футер</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#achievements">Достижения</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#departments">Отделы</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Редактирование хедера -->
            <div class="tab-pane fade show active" id="header">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Логотип (путь к файлу)</label>
                        <input type="text" name="logo" class="form-control" value="<?= htmlspecialchars($header['logo']) ?>">
                    </div>
                    
                    <h5>Меню</h5>
                    <div id="menu-items">
                        <?php foreach ($header['menu'] as $index => $item): ?>
                        <div class="row mb-2 menu-item">
                            <div class="col-md-5">
                                <input type="text" name="menu[<?= $index ?>][title]" class="form-control" 
                                       value="<?= htmlspecialchars($item['title']) ?>" placeholder="Название">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="menu[<?= $index ?>][url]" class="form-control" 
                                       value="<?= htmlspecialchars($item['url']) ?>" placeholder="URL">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-menu-item">Удалить</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-menu-item" class="btn btn-secondary">Добавить пункт</button>
                    
                    <button type="submit" name="save_header" class="btn btn-primary">Сохранить хедер</button>
                </form>
            </div>
            
            <!-- Редактирование футера -->
            <div class="tab-pane fade" id="footer">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Текст копирайта</label>
                        <input type="text" name="copyright" class="form-control" value="<?= htmlspecialchars($footer['copyright']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Адрес</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($footer['contacts']['address']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($footer['contacts']['phone']) ?>">
                    </div>
                    
                    <h5>Социальные сети</h5>
                    <div id="social-items">
                        <?php foreach ($footer['social'] as $index => $item): ?>
                        <div class="row mb-2 social-item">
                            <div class="col-md-5">
                                <select name="social[<?= $index ?>][icon]" class="form-select">
                                    <option value="bi-facebook" <?= $item['icon'] === 'bi-facebook' ? 'selected' : '' ?>>Facebook</option>
                                    <option value="bi-telegram" <?= $item['icon'] === 'bi-telegram' ? 'selected' : '' ?>>Telegram</option>
                                    <option value="bi-instagram" <?= $item['icon'] === 'bi-instagram' ? 'selected' : '' ?>>Instagram</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="url" name="social[<?= $index ?>][url]" class="form-control" 
                                       value="<?= htmlspecialchars($item['url']) ?>" placeholder="Ссылка">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-social-item">Удалить</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-social-item" class="btn btn-secondary">Добавить соцсеть</button>
                    
                    <button type="submit" name="save_footer" class="btn btn-primary">Сохранить футер</button>
                </form>
            </div>
            
            <!-- Редактирование достижений -->
            <div class="tab-pane fade" id="achievements">
                <form method="post">
                    <div id="achievement-items">
                        <?php foreach ($achievements as $index => $item): ?>
                        <div class="row mb-2 achievement-item">
                            <div class="col-md-4">
                                <input type="text" name="achievements[<?= $index ?>][value]" class="form-control" 
                                       value="<?= htmlspecialchars($item['value']) ?>" placeholder="Значение">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="achievements[<?= $index ?>][label]" class="form-control" 
                                       value="<?= htmlspecialchars($item['label']) ?>" placeholder="Описание">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-achievement-item">Удалить</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-achievement-item" class="btn btn-secondary">Добавить достижение</button>
                    
                    <button type="submit" name="save_achievements" class="btn btn-primary">Сохранить достижения</button>
                </form>
            </div>

            <!-- Редактирование отделов -->
            <div class="tab-pane fade" id="departments">
                <form method="post">
                    <div id="department-items">
                        <?php foreach ($departments as $index => $item): ?>
                        <div class="card mb-3 department-item">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Название отдела</label>
                                    <input type="text" name="departments[<?= $index ?>][title]" class="form-control" 
                                            value="<?= htmlspecialchars($item['title']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Заведующий</label>
                                    <input type="text" name="departments[<?= $index ?>][head]" class="form-control" 
                                            value="<?= htmlspecialchars($item['head']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Телефон</label>
                                    <input type="text" name="departments[<?= $index ?>][phone]" class="form-control" 
                                            value="<?= htmlspecialchars($item['phone']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Местоположение</label>
                                    <input type="text" name="departments[<?= $index ?>][location]" class="form-control" 
                                            value="<?= htmlspecialchars($item['location']) ?>" required>
                                </div>
                                <button type="button" class="btn btn-danger remove-department-item">Удалить отдел</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-department-item" class="btn btn-secondary mb-3">Добавить отдел</button>
                    <button type="submit" name="save_departments" class="btn btn-primary mb-3">Сохранить отделы</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Динамическое добавление/удаление пунктов
    document.addEventListener('DOMContentLoaded', function() {
        // Меню
        let menuIndex = <?= count($header['menu']) ?>;
        document.getElementById('add-menu-item').addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'row mb-2 menu-item';
            div.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="menu[${menuIndex}][title]" class="form-control" placeholder="Название">
                </div>
                <div class="col-md-5">
                    <input type="text" name="menu[${menuIndex}][url]" class="form-control" placeholder="URL">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-menu-item">Удалить</button>
                </div>
            `;
            document.getElementById('menu-items').appendChild(div);
            menuIndex++;
        });

        // Социальные сети
        let socialIndex = <?= count($footer['social']) ?>;
        document.getElementById('add-social-item').addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'row mb-2 social-item';
            div.innerHTML = `
                <div class="col-md-5">
                    <select name="social[${socialIndex}][icon]" class="form-select">
                        <option value="bi-facebook">Facebook</option>
                        <option value="bi-telegram">Telegram</option>
                        <option value="bi-instagram">Instagram</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="url" name="social[${socialIndex}][url]" class="form-control" placeholder="Ссылка">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-social-item">Удалить</button>
                </div>
            `;
            document.getElementById('social-items').appendChild(div);
            socialIndex++;
        });

        // Достижения
        let achievementIndex = <?= count($achievements) ?>;
        document.getElementById('add-achievement-item').addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'row mb-2 achievement-item';
            div.innerHTML = `
                <div class="col-md-4">
                    <input type="text" name="achievements[${achievementIndex}][value]" class="form-control" placeholder="Значение">
                </div>
                <div class="col-md-6">
                    <input type="text" name="achievements[${achievementIndex}][label]" class="form-control" placeholder="Описание">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-achievement-item">Удалить</button>
                </div>
            `;
            document.getElementById('achievement-items').appendChild(div);
            achievementIndex++;
        });

        //Отделы
        let departmentIndex = <?= count($departments) ?>;
        document.getElementById('add-department-item').addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'card mb-3 department-item';
            div.innerHTML = `
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Название отдела</label>
                            <input type="text" name="departments[${departmentIndex}][title]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Заведующий</label>
                            <input type="text" name="departments[${departmentIndex}][head]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Телефон</label>
                            <input type="text" name="departments[${departmentIndex}][phone]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Местоположение</label>
                            <input type="text" name="departments[${departmentIndex}][location]" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-danger remove-department-item">Удалить отдел</button>
                    </div>
                `;
                document.getElementById('department-items').appendChild(div);
                departmentIndex++;
            });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-department-item')) {
                e.target.closest('.department-item').remove();
            }
        });

        // Обработчики удаления
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-menu-item')) {
                e.target.closest('.menu-item').remove();
            }
            if (e.target.classList.contains('remove-social-item')) {
                e.target.closest('.social-item').remove();
            }
            if (e.target.classList.contains('remove-achievement-item')) {
                e.target.closest('.achievement-item').remove();
            }
        });
    });
    </script>
</body>
</html>