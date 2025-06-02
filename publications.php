<?php
require_once 'includes/config.php';
$pageTitle = 'Публикации';
require_once 'includes/header.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
?>

<!-- Публикации -->
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-4">Научные публикации</h1>
        
        <div class="card mb-5">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Поиск по названию или автору</label>
                        <input type="text" class="form-control" id="search" name="search" value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="category" class="form-label">Категория</label>
                        <select class="form-select" id="category" name="category">
                            <option value="all" <?php echo $category == 'all' ? 'selected' : ''; ?>>Все категории</option>
                            <option value="articles" <?php echo $category == 'articles' ? 'selected' : ''; ?>>Научные статьи</option>
                            <option value="books" <?php echo $category == 'Монография' ? 'selected' : ''; ?>>Монографии</option>
                            <option value="reports" <?php echo $category == 'reports' ? 'selected' : ''; ?>>Отчеты</option>
                            <option value="conferences" <?php echo $category == 'conferences' ? 'selected' : ''; ?>>Материалы конференций</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Найти</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Список публикаций -->
        <div class="row">
            <div class="col-lg-8">
                <?php
                try {
                    $sql = "SELECT * FROM publications WHERE 1=1";
                    $params = [];
                    
                    if (!empty($search)) {
                        $sql .= " AND (title LIKE ? OR author LIKE ?)";
                        $params[] = "%$search%";
                        $params[] = "%$search%";
                    }
                    
                    if ($category != 'all') {
                        $sql .= " AND category = ?";
                        $params[] = $category;
                    }
                    
                    $sql .= " ORDER BY year DESC";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="card mb-3">
                                    <div class="card-body">
                                        <h3 class="h5 card-title">'.$row['title'].'</h3>
                                        <p class="card-subtitle mb-2 text-muted">'.$row['author'].' ('.$row['year'].')</p>
                                        <p class="card-text">'.$row['description'].'</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">'.ucfirst($row['category']).'</span>';
                                            
                            if (!empty($row['file_path'])) {
                                echo '<a href="'.$row['file_path'].'" class="btn btn-sm btn-outline-primary" download>Скачать PDF</a>';
                            } else {
                                echo '<button class="btn btn-sm btn-outline-secondary" disabled>Файл недоступен</button>';
                            }
                                            
                            echo '</div>
                                    </div>
                                </div>';
                        }
                    } else {
                        echo '<div class="alert alert-info">Публикации не найдены. Попробуйте изменить критерии поиска.</div>';
                    }
                } catch(PDOException $e) {
                    echo '<div class="alert alert-danger">Ошибка загрузки публикаций: '.$e->getMessage().'</div>';
                }
                ?>
            </div>
            
            <!-- Боковая панель -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="h5 mb-0">Популярные публикации</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM publications ORDER BY RAND() LIMIT 5");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h4 class="h6 mb-1">'.substr($row['title'], 0, 50).'...</h4>
                                            <small class="text-muted">'.$row['year'].'</small>
                                        </div>
                                        <p class="mb-1 small">'.$row['author'].'</p>
                                    </a>';
                            }
                        } catch(PDOException $e) {
                            echo '<div class="list-group-item">Ошибка загрузки данных</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>