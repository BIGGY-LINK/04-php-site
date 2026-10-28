<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact';
$errors = [];
$values = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'service' => '',
    'message' => '',
];
$formError = '';

if (!isset($_SESSION['contact_csrf_token']) || !is_string($_SESSION['contact_csrf_token'])) {
    $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['contact_csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $default) {
        $postedValue = $_POST[$field] ?? $default;
        $values[$field] = is_string($postedValue) ? trim($postedValue) : '';
    }

    $postedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
        $errors['csrf_token'] = 'Your form session expired. Please try submitting the form again.';
    }

    if ($values['name'] === '') {
        $errors['name'] = 'Please enter your name.';
    }

    if ($values['email'] === '') {
        $errors['email'] = 'Please enter your email address.';
    } elseif (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($values['service'] === '') {
        $errors['service'] = 'Please select a service.';
    } else {
        $serviceExists = false;
        foreach ($services as $service) {
            if ($service['slug'] === $values['service']) {
                $serviceExists = true;
                break;
            }
        }

        if (!$serviceExists) {
            $errors['service'] = 'Please select one of the listed services.';
        }
    }

    $messageLength = preg_match_all('/./us', $values['message']);
    if ($values['message'] === '') {
        $errors['message'] = 'Please enter a message.';
    } elseif ($messageLength === false) {
        $errors['message'] = 'Please enter a message using valid text.';
    } elseif ($messageLength < 10 || $messageLength > 1000) {
        $errors['message'] = 'Your message must be between 10 and 1,000 characters.';
    }

    if ($errors === []) {
        $selectedServiceTitle = '';
        foreach ($services as $service) {
            if ($service['slug'] === $values['service']) {
                $selectedServiceTitle = $service['title'];
                break;
            }
        }

        $storageDirectory = __DIR__ . '/storage';
        if (!is_dir($storageDirectory) && !@mkdir($storageDirectory, 0775, true) && !is_dir($storageDirectory)) {
            error_log('Unable to create contact form storage directory: ' . $storageDirectory);
            $formError = 'Sorry, we could not save your message right now. Please try again later.';
        } else {
            $submission = [
                'timestamp' => date(DATE_ATOM),
                'name' => $values['name'],
                'email' => $values['email'],
                'phone' => $values['phone'],
                'service' => $selectedServiceTitle,
                'message' => $values['message'],
            ];
            $jsonLine = json_encode($submission, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($jsonLine === false) {
                error_log('Unable to encode contact form submission: ' . json_last_error_msg());
                $formError = 'Sorry, we could not save your message right now. Please try again later.';
            } else {
                $bytesWritten = file_put_contents(
                    $storageDirectory . '/messages.jsonl',
                    $jsonLine . PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );

                if ($bytesWritten === false) {
                    error_log('Unable to append contact form submission to messages.jsonl.');
                    $formError = 'Sorry, we could not save your message right now. Please try again later.';
                } else {
                    $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));
                    header('Location: contact.php?sent=1', true, 303);
                    exit;
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <p class="eyebrow">Let's make something</p>
        <h1>Have a project in mind?</h1>
        <p>Tell us a little about it. We would love to hear what you are working on.</p>
    </div>
</section>

<section class="section container content-narrow contact-panel">
    <h2>Start a conversation</h2>
    <p>Fill out the form and we will get back to you.</p>

    <?php if (isset($_GET['sent']) && $_GET['sent'] === '1'): ?>
        <p class="form-alert form-alert-success" role="status">Thanks for reaching out. Your message has been sent.</p>
    <?php endif; ?>

    <?php if ($formError !== ''): ?>
        <p class="form-alert form-alert-error" role="alert"><?= e($formError) ?></p>
    <?php endif; ?>

    <?php if (isset($errors['csrf_token'])): ?>
        <p class="form-alert form-alert-error" role="alert"><?= e($errors['csrf_token']) ?></p>
    <?php endif; ?>

    <form class="contact-form" method="post" action="contact.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <div class="form-field">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" autocomplete="name" required
                   value="<?= e($values['name']) ?>"
                   <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?>>
            <?php if (isset($errors['name'])): ?>
                <p class="field-error" id="name-error"><?= e($errors['name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" autocomplete="email" required
                   value="<?= e($values['email']) ?>"
                   <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>>
            <?php if (isset($errors['email'])): ?>
                <p class="field-error" id="email-error"><?= e($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-field">
            <label for="phone">Phone <span class="field-hint">(optional)</span></label>
            <input id="phone" name="phone" type="tel" autocomplete="tel"
                   value="<?= e($values['phone']) ?>">
        </div>

        <div class="form-field">
            <label for="service">Service</label>
            <select id="service" name="service" required
                    <?= isset($errors['service']) ? 'aria-invalid="true" aria-describedby="service-error"' : '' ?>>
                <option value="">Choose a service</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= e($service['slug']) ?>" <?= $values['service'] === $service['slug'] ? 'selected' : '' ?>>
                        <?= e($service['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['service'])): ?>
                <p class="field-error" id="service-error"><?= e($errors['service']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-field">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" required
                      <?= isset($errors['message']) ? 'aria-invalid="true" aria-describedby="message-error"' : '' ?>><?= e($values['message']) ?></textarea>
            <?php if (isset($errors['message'])): ?>
                <p class="field-error" id="message-error"><?= e($errors['message']) ?></p>
            <?php endif; ?>
        </div>

        <button class="button" type="submit">Send message</button>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
