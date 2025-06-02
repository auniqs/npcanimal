<?php
require_once 'includes/config.php';
$pageTitle = 'О центре';

// Загружаем данные специалистов
$specialists = json_decode(file_get_contents('data/specialists.json'), true);

require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-4">О Научно-Практическом Центре</h1>
                <p class="lead">Наш центр - ведущее научное учреждение в области животноводства в Республике Беларусь.</p>
                <p>Основанный в 1927 году, наш центр занимается фундаментальными и прикладными исследованиями в области животноводства, направленными на повышение продуктивности и устойчивости сельского хозяйства.</p>
                <p>Мы сотрудничаем с ведущими научными учреждениями Европы и СНГ, внедряем инновационные технологии в сельское хозяйство Беларуси.</p>
                <div class="mt-4">
                    <a href="contacts.php" class="btn btn-outline-primary">Контакты</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/5nqR-fvfy70?start=35" allowfullscreen></iframe>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-muted">Видео о нашем центре</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Наша миссия -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <h2 class="fw-bold mb-4">Наша миссия</h2>
                <p>Мы стремимся к развитию научно обоснованных, экологически устойчивых и экономически эффективных систем животноводства, способствующих продовольственной безопасности страны.</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Совершенствование разводимых в республике и выведение новых пород, кроссов, типов и линий сельскохозяйственных животных, птиц и рыб.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Разработка новых составов комбикормов и кормовых добавок на основе местных источников сырья.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Изыскание путей повышения эффективности трансформации энергии корма в животноводческую продукцию.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Совершенствование технологии кормления, содержания и использования животных, птиц и рыб.</li>
                </ul>
            </div>
            <div class="col-lg-6 order-lg-1">
                <img src="images/mission.jpg" alt="Наша миссия" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Наши ведущие специалисты</h2>
            <button id="showAllBtn" class="btn btn-outline-primary">Показать больше</button>
        </div>
        
        <div class="row g-4" id="specialistsContainer">
            <?php 
            // Показываем только первых 4 специалистов
            $displayedSpecialists = array_slice($specialists, 0, 4);
            foreach ($displayedSpecialists as $specialist): ?>
                <div class="col-md-6 col-lg-3 specialist-card">
                    <div class="card h-100">
                        <img src="<?= $specialist['image'] ?>" class="card-img-top" alt="<?= $specialist['name'] ?>">
                        <div class="card-body text-center">
                            <h3 class="h5 card-title mb-1"><?= $specialist['name'] ?></h3>
                            <p class="text-muted small mb-2"><?= $specialist['position'] ?></p>
                            <p class="card-text small"><?= $specialist['bio'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const showAllBtn = document.getElementById('showAllBtn');
    const specialistsContainer = document.getElementById('specialistsContainer');
    const allSpecialists = <?= json_encode($specialists) ?>;
    let showingAll = false;

    showAllBtn.addEventListener('click', function() {
        showingAll = !showingAll;
        
        if (showingAll) {
            // Показываем всех специалистов
            specialistsContainer.innerHTML = '';
            allSpecialists.forEach(specialist => {
                specialistsContainer.innerHTML += `
                    <div class="col-md-6 col-lg-3 specialist-card">
                        <div class="card h-100">
                            <img src="${specialist.image}" class="card-img-top" alt="${specialist.name}">
                            <div class="card-body text-center">
                                <h3 class="h5 card-title mb-1">${specialist.name}</h3>
                                <p class="text-muted small mb-2">${specialist.position}</p>
                                <p class="card-text small">${specialist.bio}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            showAllBtn.textContent = 'Свернуть';
        } else {
            // Показываем только первых 4
            specialistsContainer.innerHTML = '';
            allSpecialists.slice(0, 4).forEach(specialist => {
                specialistsContainer.innerHTML += `
                    <div class="col-md-6 col-lg-3 specialist-card">
                        <div class="card h-100">
                            <img src="${specialist.image}" class="card-img-top" alt="${specialist.name}">
                            <div class="card-body text-center">
                                <h3 class="h5 card-title mb-1">${specialist.name}</h3>
                                <p class="text-muted small mb-2">${specialist.position}</p>
                                <p class="card-text small">${specialist.bio}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            showAllBtn.textContent = 'Показать больше';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>