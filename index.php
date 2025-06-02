<?php
require_once 'includes/config.php';
$pageTitle = 'Главная';
require_once 'includes/header.php';
?>

<!-- Герой-секция -->
<section class="hero-section bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Научно-Практический Центр по животноводству</h1>
                <p class="lead mb-4">Передовые исследования в области животноводства для устойчивого развития сельского хозяйства Беларуси.</p>
                <div class="d-flex gap-3">
                    <a href="about.php" class="btn btn-primary btn-lg px-4">О центре</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="images/hero-image.jfif" alt="Животноводство" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Основные направления -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Основные направления деятельности</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                            <i class="bi bi-box2 text-primary fs-1"></i>
                        </div>
                        <h3 class="h5">Разведение КРС</h3>
                        <p class="text-muted">Селекция и генетика крупного рогатого скота, повышение продуктивности.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                            <i class="bi bi-droplet text-primary fs-1"></i>
                        </div>
                        <h3 class="h5">Корма и кормление</h3>
                        <p class="text-muted">Разработка эффективных рационов и технологий кормления.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                            <i class="bi bi-shield-plus text-primary fs-1"></i>
                        </div>
                        <h3 class="h5">Ветеринария</h3>
                        <p class="text-muted">Профилактика и лечение заболеваний сельскохозяйственных животных.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Последние новости -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Последние новости</h2>
            <a href="news.php" class="btn btn-outline-primary">Все новости</a>
        </div>
        
        <div class="row g-4">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM news ORDER BY publication_date DESC LIMIT 3");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="col-md-4">
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
            } catch(PDOException $e) {
                echo '<div class="col-12"><div class="alert alert-danger">Ошибка загрузки новостей: '.$e->getMessage().'</div></div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Наши достижения -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Наши достижения</h2>
        <div class="row g-4">
            <?php 
            $achievements = json_decode(file_get_contents('data/achievements.json'), true);
            foreach ($achievements as $achievement): ?>
            <div class="col-md-3 col-6 text-center">
                <div class="display-4 text-primary fw-bold"><?= $achievement['value'] ?></div>
                <p class="text-muted"><?= $achievement['label'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>