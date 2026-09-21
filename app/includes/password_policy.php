<?php
declare(strict_types=1);

/**
 * Password policy, following UK NCSC guidance
 * (https://www.ncsc.gov.uk/collection/passwords):
 *
 *  - No composition rules ("must contain a symbol"). NCSC specifically
 *    advises against this - it trains people to write Password1! instead
 *    of something actually harder to guess, and does little to stop
 *    credential-stuffing or breach-list attacks anyway.
 *  - A reasonable minimum length, encouraging long passphrases (NCSC's
 *    public "three random words" advice) over short-but-complex ones.
 *  - Checked against a known-breached-password database (Have I Been
 *    Pwned's Pwned Passwords), not just a short local blocklist -
 *    NCSC's guidance points at exactly this kind of check.
 *  - Blocked from containing the account's own username.
 *  - No mandatory periodic expiry anywhere in this app (NCSC advises
 *    against forcing regular changes - there's simply no expiry logic
 *    to remove).
 *  - Failed logins are throttled (see LOGIN_MAX_ATTEMPTS in auth.php),
 *    which NCSC recommends instead of (or as well as) complexity rules.
 */

const PASSWORD_MIN_LENGTH = 12;

// bcrypt (PHP's password_hash default) silently ignores anything past 72
// bytes. Rather than let a long passphrase get quietly weakened, reject
// it outright so the person knows to shorten it.
const PASSWORD_MAX_BYTES = 72;

/** Common passwords and fragments worth blocking outright even offline,
 *  before or instead of the breach-database check. Matched as a
 *  case-insensitive substring, so "welcome1234" is caught by "welcome". */
function common_password_fragments(): array
{
    return [
        'password', 'passw0rd', 'welcome', 'letmein', 'qwerty', 'qwertyuiop', 'asdfgh',
        'admin', 'administrator', 'login', 'master', 'monkey', 'dragon', 'football',
        'baseball', 'superman', 'trustno1', 'sunshine', 'princess', 'shadow', 'iloveyou',
        'starwars', 'whatever', 'freedom', 'access', 'flower', 'hunter', 'ranger',
        'change', 'default', 'guest', 'testtest', 'temptemp',
        '123456', '1234567', '12345678', '123456789', '1234567890', '0123456789',
        '111111', '000000', 'abc123', 'abcdefgh', 'zxcvbnm',
    ];
}

/** True if $s contains a run of $runLength+ sequential characters, e.g. "1234" or "fghi". */
function has_sequential_run(string $s, int $runLength): bool
{
    $chars = str_split(preg_replace('/[^a-z0-9]/', '', $s) ?? '');
    $ascRun = 1;
    $descRun = 1;
    for ($i = 1; $i < count($chars); $i++) {
        $prev = ord($chars[$i - 1]);
        $cur = ord($chars[$i]);
        $ascRun = ($cur === $prev + 1) ? $ascRun + 1 : 1;
        $descRun = ($cur === $prev - 1) ? $descRun + 1 : 1;
        if ($ascRun >= $runLength || $descRun >= $runLength) {
            return true;
        }
    }
    return false;
}

/**
 * Checks a password against Have I Been Pwned's Pwned Passwords database,
 * using the k-anonymity API: only the first 5 characters of the
 * password's SHA-1 hash are ever sent over the network. The password
 * itself, and the rest of its hash, never leave this server.
 *
 * Returns true if it's known-breached, false if it isn't, or null if the
 * check couldn't be completed at all (no internet access, host blocks
 * outbound requests, the API is down, etc). Callers should treat null as
 * "couldn't check" and NOT block on it - this must fail open, or a
 * network hiccup would lock people out of creating accounts entirely.
 */
function is_pwned_password(string $password): ?bool
{
    $sha1 = strtoupper(sha1($password));
    $prefix = substr($sha1, 0, 5);
    $suffix = substr($sha1, 5);
    $url = 'https://api.pwnedpasswords.com/range/' . $prefix;
    $response = false;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_USERAGENT      => 'arboretum-password-check',
        ]);
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body !== false && $httpCode === 200) {
            $response = $body;
        }
    } elseif (function_exists('ini_get') && ini_get('allow_url_fopen')) {
        $context = stream_context_create(['http' => [
            'method'        => 'GET',
            'timeout'       => 3,
            'header'        => "User-Agent: arboretum-password-check\r\n",
            'ignore_errors' => true,
        ]]);
        $body = @file_get_contents($url, false, $context);
        $status = $http_response_header[0] ?? '';
        if ($body !== false && str_contains($status, '200')) {
            $response = $body;
        }
    }

    if ($response === false || $response === '') {
        return null; // couldn't check - fail open
    }

    foreach (preg_split('/\r\n|\n/', trim($response)) as $line) {
        $parts = explode(':', trim($line));
        if (count($parts) === 2 && strcasecmp($parts[0], $suffix) === 0) {
            return ((int)$parts[1]) > 0;
        }
    }
    return false;
}

/** Returns a list of human-readable problems with $password, or [] if it passes. */
function password_policy_errors(string $password, string $username = ''): array
{
    $errors = [];
    $lower = strtolower($password);

    if (mb_strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
    }
    if (strlen($password) > PASSWORD_MAX_BYTES) {
        $errors[] = 'Password must be ' . PASSWORD_MAX_BYTES . ' characters or fewer - anything longer gets silently cut down by the hashing algorithm and gains nothing.';
    }

    foreach (common_password_fragments() as $fragment) {
        if (str_contains($lower, $fragment)) {
            $errors[] = 'That password is too easy to guess (contains a common word or pattern like "' . $fragment . '"). Three random unrelated words works better than a short word with a number stuck on the end.';
            break;
        }
    }

    if ($username !== '' && mb_strlen($username) >= 3 && str_contains($lower, strtolower($username))) {
        $errors[] = "Password can't contain your username.";
    }

    if (preg_match('/(.)\1{3,}/', $password)) {
        $errors[] = 'Password repeats the same character too many times in a row.';
    }

    if (has_sequential_run($lower, 4)) {
        $errors[] = 'Password contains a run of sequential characters (like "1234" or "abcd") - too guessable.';
    }

    // Skip the breach-database lookup if the password already failed a
    // free local check - no point spending a network round trip on a
    // password that's being rejected anyway.
    if (!$errors) {
        $pwned = is_pwned_password($password);
        if ($pwned === true) {
            $errors[] = 'This password has appeared in known data breaches, so it\'s not safe to use even though it\'s not on our local list. Pick a different one.';
        }
    }

    return $errors;
}
