<?php
declare(strict_types=1);

$pageTitle = 'About';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <p class="eyebrow">A little about us</p>
        <h1>Creative minds, thoughtful work.</h1>
        <p>We believe every business has a story worth sharing—and great design helps people remember it.</p>
    </div>
</section>

<section class="section container content-narrow">
    <h2>Design with purpose</h2>
    <p>EFLASH GRAPHIXS is a fictional independent creative studio built around a simple idea: clear, considered design can help a business stand out. We bring curiosity, care, and a collaborative spirit to every project.</p>
    <p>Whether you are starting from scratch or ready for a refresh, we work with you to shape visuals that feel true to your brand.</p>
</section>

<section class="section container">
    <div class="section-heading">
        <p class="eyebrow">Meet the people behind the work</p>
        <h2>Our creative team</h2>
    </div>
    <div class="team-grid">
        <?php foreach ($team as $member): ?>
            <article class="team-card">
                <h3><?= e($member['name']) ?></h3>
                <p class="team-role"><?= e($member['role']) ?></p>
                <p><?= e($member['bio']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
