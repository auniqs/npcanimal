<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$pageTitle = 'Админ-панель';
require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Боковое меню -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="site_manage.php">
                            <i class="bi bi-layout-text-sidebar-reverse"></i> Управление сайтом
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="news_manage.php">
                            <i class="bi bi-newspaper me-2"></i> Управление новостями
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="publications_manage.php">
                            <i class="bi bi-journal-text me-2"></i> Управление публикациями
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="specialists_manage.php">
                             <i class="bi bi-people me-2"></i> Управление специалистами
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth.php?action=logout">
                            <i class="bi bi-box-arrow-right me-2"></i> Выход
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Основное содержимое -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Панель управления</h1>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Новости</h5>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) FROM news");
                                $newsCount = $stmt->fetchColumn();
                                echo '<p class="card-text display-5">'.$newsCount.'</p>';
                            } catch(PDOException $e) {
                                echo '<p class="card-text">Ошибка загрузки</p>';
                            }
                            ?>
                            <a href="news_manage.php" class="text-white">Управление новостями <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Публикации</h5>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) FROM publications");
                                $pubCount = $stmt->fetchColumn();
                                echo '<p class="card-text display-5">'.$pubCount.'</p>';
                            } catch(PDOException $e) {
                                echo '<p class="card-text">Ошибка загрузки</p>';
                            }
                            ?>
                            <a href="publications_manage.php" class="text-white">Управление публикациями <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Последние новости</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
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
                                    $stmt = $pdo->query("SELECT * FROM news ORDER BY publication_date DESC LIMIT 5");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<tr>
                                                <td>'.$row['id'].'</td>
                                                <td>'.substr($row['title'], 0, 30).'...</td>
                                                <td>'.date('d.m.Y', strtotime($row['publication_date'])).'</td>
                                                <td>'.$row['author'].'</td>
                                                <td>
                                                    <a href="news_manage.php?action=edit&id='.$row['id'].'" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="news_manage.php?action=delete&id='.$row['id'].'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Вы уверены?\')"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>';
                                    }
                                } catch(PDOException $e) {
                                    echo '<tr><td colspan="5" class="text-center text-danger">Ошибка загрузки новостей</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>