<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<?php
require_once 'includes/config.php';
$pageTitle = 'Новости';
require_once 'includes/header.php';

// Получение конкретной новости или списка новостей
if (isset($_GET['id'])) {
    $newsId = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$newsId]);
        $newsItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$newsItem) {
            header("Location: news.php");
            exit();
        }
        
        // Страница одной новости
        ?>
        <section class="py-5">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page"><?php echo substr($newsItem['title'], 0, ); ?>...</li>
                    </ol>
                </nav>
                
                <article>
                    <header class="mb-4">
                        <h1 class="fw-bold mb-3"><?php echo $newsItem['title']; ?></h1>
                        <div class="text-muted mb-3">
                            <i class="bi bi-calendar me-2"></i> <?php echo date('d.m.Y', strtotime($newsItem['publication_date'])); ?>
                            <i class="bi bi-person ms-3 me-2"></i> <?php echo $newsItem['author']; ?>
                        </div>
                        <?php if (!empty($newsItem['image_path'])): ?>
                        <img src="<?php echo $newsItem['image_path']; ?>" class="img-fluid rounded mb-4" alt="<?php echo $newsItem['title']; ?>">
                        <?php endif; ?>
                    </header>
                    
                    <div class="news-content mb-5">
                        <?php echo nl2br($newsItem['content']); ?>
                    </div>
                    
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Опубликовано: <?php echo date('d.m.Y H:i', strtotime($newsItem['publication_date'])); ?></span>
                            </div>
                            <div>
                                <a href="news.php" class="btn btn-sm btn-outline-primary">Все новости</a>
                            </div>
                        </div>
                    </div>
                </article>
                
                <!-- Похожие новости -->
                <section class="mt-5 pt-4">
                    <h2 class="h4 mb-4">Другие новости</h2>
                    <div class="row g-4">
                        <?php
                        try {
                            $stmt = $pdo->prepare("SELECT * FROM news WHERE id != ? ORDER BY publication_date DESC LIMIT 3");
                            $stmt->execute([$newsId]);
                            
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="col-md-4">
                                        <div class="card h-100">
                                            <img src="'.(!empty($row['image_path']) ? $row['image_path'] : 'images/news-placeholder.jpg').'" class="card-img-top" alt="'.$row['title'].'">
                                            <div class="card-body">
                                                <h3 class="h5 card-title">'.$row['title'].'</h3>
                                                <p class="card-text text-muted small">'.substr($row['content'], 0, 80).'...</p>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <a href="news.php?id='.$row['id'].'" class="btn btn-sm btn-outline-primary">Читать</a>
                                                <small class="text-muted float-end">'.date('d.m.Y', strtotime($row['publication_date'])).'</small>
                                            </div>
                                        </div>
                                    </div>';
                            }
                        } catch(PDOException $e) {
                            echo '<div class="col-12"><div class="alert alert-danger">Ошибка загрузки новостей: '.$e->getMessage().'</div></div>';
                        }
                        ?>
                    </div>
                </section>
            </div>
        </section>
        <?php
    } catch(PDOException $e) {
        echo '<div class="container py-5"><div class="alert alert-danger">Ошибка загрузки новости: '.$e->getMessage().'</div></div>';
    }
} else {
    // Список всех новостей
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 6;
    $offset = ($page - 1) * $perPage;
    
    try {
        // Общее количество новостей
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM news");
        $total = $totalStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);
        
        // Получение новостей для текущей страницы
        $stmt = $pdo->prepare("SELECT * FROM news ORDER BY publication_date DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        ?>
        <section class="py-5">
            <div class="container">
                <h1 class="display-5 fw-bold mb-4">Новости центра</h1>
                
                <div class="row g-4 mb-4">
                    <?php
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="col-md-6 col-lg-4">
                                <div class="card h-100">
                                    <img src="'.(!empty($row['image_path']) ? $row['image_path'] : 'images/news-placeholder.jpg').'" class="card-img-top" alt="'.$row['title'].'">
                                    <div class="card-body">
                                        <h3 class="h5 card-title">'.$row['title'].'</h3>
                                        <p class="card-text text-muted">'.substr($row['content'], 0, 100).'...</p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <small class="text-muted">'.date('d.m.Y', strtotime($row['publication_date'])).'</small>
                                        <a href="news.php?id='.$row['id'].'" class="btn btn-sm btn-outline-primary float-end">Читать</a>
                                    </div>
                                </div>
                            </div>';
                    }
                    ?>
                </div>
                
                <!-- Пагинация -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Навигация по страницам">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="news.php?page=<?php echo $page - 1; ?>" aria-label="Предыдущая">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="news.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="news.php?page=<?php echo $page + 1; ?>" aria-label="Следующая">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </section>
        <?php
    } catch(PDOException $e) {
        echo '<div class="container py-5"><div class="alert alert-danger">Ошибка загрузки новостей: '.$e->getMessage().'</div></div>';
    }
}
?>

<?php require_once 'includes/footer.php'; ?>