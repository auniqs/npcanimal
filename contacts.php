<?php
require_once 'includes/config.php';
$pageTitle = 'Контакты';
require_once 'includes/header.php';

$departments = json_decode(file_get_contents('data/departments.json'), true);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Обработка формы обратной связи
$feedbackSent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $message = sanitize($_POST['message']);
    
    // Валидация
    if (empty($name)) {
        $errors['name'] = 'Пожалуйста, введите ваше имя';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Пожалуйста, введите корректный email';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Пожалуйста, введите ваше сообщение';
    }
    
    if (empty($errors)) {
    $botToken = '8190086436:AAESvZZwJxuM_Oscu3DLcZLgU_YPJYbdtIU'; // Например: '6123456789:ABC-DEF1234ghIkl-zyx57W2v1u123ew11'
    $chatId = '1040489076'; // Например: '123456789'
    
    $text = "📨 *Новое сообщение с сайта*:\n\n"
        . "▪ *Имя*: $name\n"
        . "▪ *Email*: $email\n"
        . "▪ *Телефон*: $phone\n"
        . "▪ *Сообщение*: $message";
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true
    ];
    
    // Используем cURL вместо file_get_contents (надежнее)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && json_decode($response)->ok) {
        $feedbackSent = true;
    } else {
        $errorDetails = $httpCode != 200 ? "HTTP код: $httpCode" : "Ответ API: $response";
        $errors['telegram'] = "Ошибка отправки в Telegram. Техническая информация: $errorDetails";
    }
}
}
?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
<!-- Контакты -->
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-4">Контакты</h1>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h4 mb-4">Контактная информация</h2>
                        
                        <div class="mb-4">
                            <h3 class="h5">Адрес</h3>
                            <p><i class="bi bi-geo-alt-fill text-primary me-2"></i> 222160, Республика Беларусь, г. Жодино, ул. Фрунзе, 11</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="h5">Телефоны</h3>
                            <p><i class="bi bi-telephone-fill text-primary me-2"></i> +375 (17) 755-39-62 (приемная)</p>
                            <p><i class="bi bi-telephone-fill text-primary me-2"></i> +375 (17) 756-89-34 (факс)</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="h5">Электронная почта</h3>
                            <p><i class="bi bi-envelope-fill text-primary me-2"></i> info@belniig.by</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="h5">Режим работы</h3>
                            <p><i class="bi bi-clock-fill text-primary me-2"></i> Понедельник - Пятница: 8:30 - 17:30</p>
                            <p><i class="bi bi-clock-fill text-primary me-2"></i> Обед: 13:00 - 14:00</p>
                            <p><i class="bi bi-clock-fill text-primary me-2"></i> Суббота, Воскресенье: выходной</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h4 mb-4">Форма обратной связи</h2>
                        
                        <?php if ($feedbackSent): ?>
                        <div class="alert alert-success">
                            <h3 class="h5">Спасибо за ваше сообщение!</h3>
                            <p class="mb-0">Мы рассмотрим ваше обращение и ответим в ближайшее время.</p>
                        </div>
                        <?php else: ?>
                        
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h3 class="h5">Пожалуйста, исправьте следующие ошибки:</h3>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Ваше имя <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
                                <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                                <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Сообщение <span class="text-danger">*</span></label>
                                <textarea class="form-control <?php echo isset($errors['message']) ? 'is-invalid' : ''; ?>" id="message" name="message" rows="5"><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>
                                <?php if (isset($errors['message'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['message']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="consent" name="consent" required>
                                <label class="form-check-label" for="consent">Я согласен на обработку персональных данных</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Отправить сообщение</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Карта -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-map"></i> Мы на карте
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Отделы -->
        <section class="mt-5 pt-4">
        <h2 class="text-center mb-5">Наши отделы</h2>
        <div class="accordion" id="departmentsAccordion">
            <?php foreach ($departments as $index => $department): ?>
            <div class="accordion-item">
                <h3 class="accordion-header" id="heading<?= $index ?>">
                    <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>">
                        <?= htmlspecialchars($department['title']) ?>
                    </button>
                </h3>
                <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#departmentsAccordion">
                    <div class="accordion-body">
                        <p><strong>Заведующий:</strong> <?= htmlspecialchars($department['head']) ?></p>
                        <p><strong>Телефон:</strong> <?= htmlspecialchars($department['phone']) ?></p>
                        <p><strong>Местоположение:</strong> <?= htmlspecialchars($department['location']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([54.105889, 28.359179], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            L.marker([54.105889, 28.359179]).addTo(map)
                .bindPopup("<b>НПЦ по животноводству</b><br>ул. Фрунзе, 11")
                .openPopup();
        });
</script>

<?php require_once 'includes/footer.php'; ?>