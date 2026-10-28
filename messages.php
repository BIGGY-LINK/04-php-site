<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Messages';
$loginError = '';
$storageError = '';
$messages = [];

if (!isset($_SESSION['messages_csrf_token']) || !is_string($_SESSION['messages_csrf_token'])) {
    $_SESSION['messages_csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['messages_csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
        $loginError = 'Your session expired. Refresh the page and try again.';
    } elseif (isset($_POST['logout'])) {
        unset($_SESSION['messages_authenticated']);
        session_regenerate_id(true);
        $_SESSION['messages_csrf_token'] = bin2hex(random_bytes(32));
        header('Location: messages.php', true, 303);
        exit;
    } else {
        $submittedPassword = $_POST['password'] ?? '';
        if (is_string($submittedPassword) && hash_equals($messagesPassword, $submittedPassword)) {
            session_regenerate_id(true);
            $_SESSION['messages_authenticated'] = true;
            $_SESSION['messages_csrf_token'] = bin2hex(random_bytes(32));
            header('Location: messages.php', true, 303);
            exit;
        }

        $loginError = 'That password was not correct. Please try again.';
    }
}

$isAuthenticated = ($_SESSION['messages_authenticated'] ?? false) === true;

if ($isAuthenticated) {
    $messagesFile = __DIR__ . '/storage/messages.jsonl';
    if (is_file($messagesFile)) {
        $lines = file($messagesFile, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            error_log('Unable to read contact submissions from messages.jsonl.');
            $storageError = 'Submissions could not be loaded. Check the storage file permissions.';
        } else {
            foreach ($lines as $line) {
                if ($line === '') {
                    continue;
                }

                $message = json_decode($line, true);
                if (
                    !is_array($message)
                    || !isset($message['timestamp'], $message['name'], $message['email'], $message['phone'], $message['service'], $message['message'])
                    || !is_string($message['timestamp'])
                    || !is_string($message['name'])
                    || !is_string($message['email'])
                    || !is_string($message['phone'])
                    || !is_string($message['service'])
                    || !is_string($message['message'])
                    || strtotime($message['timestamp']) === false
                ) {
                    error_log('Skipping an invalid contact submission line in messages.jsonl.');
                    continue;
                }

                $messages[] = $message;
            }

            usort(
                $messages,
                static fn (array $first, array $second): int =>
                    strtotime($second['timestamp']) <=> strtotime($first['timestamp'])
            );
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <p class="eyebrow">Private inbox</p>
        <h1>Contact submissions</h1>
        <p>Review messages sent through the website contact form.</p>
    </div>
</section>

<section class="section container messages-panel">
    <?php if (!$isAuthenticated): ?>
        <div class="content-narrow">
            <h2>Sign in to view submissions</h2>
            <p>This page is for local learning use. Enter the configured password to continue.</p>
            <?php if ($loginError !== ''): ?>
                <p class="form-alert form-alert-error" role="alert"><?= e($loginError) ?></p>
            <?php endif; ?>
            <form class="contact-form" method="post" action="messages.php">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <div class="form-field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <button class="button" type="submit">View messages</button>
            </form>
        </div>
    <?php else: ?>
        <div class="messages-heading">
            <h2>Inbox <span class="count-badge"><?= e((string) count($messages)) ?></span></h2>
            <form method="post" action="messages.php">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <button class="button button-secondary" type="submit" name="logout" value="1">Sign out</button>
            </form>
        </div>

        <?php if ($storageError !== ''): ?>
            <p class="form-alert form-alert-error" role="alert"><?= e($storageError) ?></p>
        <?php elseif ($messages === []): ?>
            <p>No submissions yet.</p>
        <?php else: ?>
            <div class="messages-table-wrap">
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th scope="col">Received</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Service</th>
                            <th scope="col">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?= e($message['timestamp']) ?></td>
                                <td><?= e($message['name']) ?></td>
                                <td><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></td>
                                <td><?= e($message['phone'] !== '' ? $message['phone'] : '—') ?></td>
                                <td><?= e($message['service']) ?></td>
                                <td class="message-cell"><?= e($message['message']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
