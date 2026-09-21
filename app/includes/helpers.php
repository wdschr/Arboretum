<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function esc(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * json_encode() for embedding data inside an inline <script> block.
 * Deliberately does NOT pass JSON_UNESCAPED_SLASHES: without it,
 * forward slashes come out as \/, so user-entered text containing
 * "</script" (a note, a URL, anything) can never actually close the
 * surrounding <script> tag early and truncate the page's JavaScript.
 */
function json_for_script($data): string
{
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

function set_flash(string $type, string $message): void
{
    start_session();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** Reads and clears any queued flash messages. */
function take_flashes(): array
{
    start_session();
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

function post_string(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function get_int(string $key, int $default = 0): int
{
    return isset($_GET[$key]) && ctype_digit((string)$_GET[$key]) ? (int)$_GET[$key] : $default;
}
