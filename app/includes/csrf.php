<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

/** Dies with a 400 if the posted token doesn't match. Call at the top of every POST handler. */
function csrf_verify(): void
{
    start_session();
    $posted = $_POST['csrf_token'] ?? '';
    if (!is_string($posted) || !hash_equals($_SESSION['csrf_token'] ?? '', $posted)) {
        http_response_code(400);
        die('Your session expired or the form was resubmitted. Go back and try again.');
    }
}
