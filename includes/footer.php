<?php
$footerData = json_decode(file_get_contents(__DIR__ . '/../data/footer.json'), true);
?>
<!-- Подвал -->
<footer class="bg-dark text-white pt-4 pb-2 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Научно-Практический Центр</h5>
                <p>Национальной Академии Наук по животноводству</p>
                <p><?= htmlspecialchars($footerData['copyright']) ?></p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Контакты</h5>
                <p><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($footerData['contacts']['address']) ?></p>
                <p><i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($footerData['contacts']['phone']) ?></p>
                <p><i class="bi bi-envelope-fill"> info@belniig.by</i></p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Социальные сети</h5>
                <?php foreach ($footerData['social'] as $social): ?>
                    <a href="<?= htmlspecialchars($social['url']) ?>" class="text-white me-2" target="_blank">
                        <i class="bi <?= htmlspecialchars($social['icon']) ?> fs-4"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>