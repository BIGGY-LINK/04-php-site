<?php
declare(strict_types=1);

$pageTitle = 'Home';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-content">
        <p class="eyebrow">Design that gets noticed</p>
        <h1>Make your brand impossible to ignore.</h1>
        <p class="hero-copy">We turn big ideas into memorable visuals, helping ambitious businesses show up with confidence.</p>
        <a class="button" href="services.php">Explore our services</a>
    </div>
</section>

<section class="section container intro">
    <p class="eyebrow">Welcome to EFLASH GRAPHIXS</p>
    <h2>Good design makes a lasting impression.</h2>
    <p>From a fresh logo to a complete brand identity, we create thoughtful design that tells your story and connects with your customers.</p>
    <a class="text-link" href="about.php">Get to know us <span aria-hidden="true">&rarr;</span></a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
