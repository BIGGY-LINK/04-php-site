<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

$requestedSlug = isset($_GET['slug']) && is_string($_GET['slug']) ? $_GET['slug'] : '';
$selectedService = null;

foreach ($services as $service) {
    if ($service['slug'] === $requestedSlug) {
        $selectedService = $service;
        break;
    }
}

if ($selectedService === null) {
    http_response_code(404);
    $pageTitle = 'Service not found';
} else {
    $pageTitle = $selectedService['title'];
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <p class="eyebrow">Our services</p>
        <?php if ($selectedService === null): ?>
            <h1>We couldn't find that service.</h1>
            <p>It may have moved or the link may be incorrect. Browse our services to find what you need.</p>
            <a class="button" href="services.php">View all services</a>
        <?php else: ?>
            <h1><?= e($selectedService['title']) ?></h1>
            <p><?= e($selectedService['description']) ?></p>
        <?php endif; ?>
    </div>
</section>

<?php if ($selectedService !== null): ?>
    <section class="section container content-narrow service-detail">
        <span class="service-icon" aria-hidden="true"><?= e($selectedService['icon']) ?></span>
        <h2>Service details</h2>
        <p><?= e($selectedService['description']) ?></p>
        <p class="service-price">Starting at <?= e(formatPrice($selectedService['price'])) ?></p>
        <a class="text-link" href="contact.php">Ask us about this service <span aria-hidden="true">&rarr;</span></a>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
