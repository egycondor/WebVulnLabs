<?php

declare(strict_types=1);

session_start();

/**
 * CorpConnect intentionally avoids ldap_escape() on user-controlled filter fragments.
 */

function env(string $key, string $default = ''): string
{
    $v = getenv($key);
    return ($v === false || $v === '') ? $default : $v;
}

function ldap_settings(): array
{
    return [
        'host' => env('LDAP_HOST', 'ldap'),
        'port' => (int) env('LDAP_PORT', '389'),
        'base_dn' => env('LDAP_BASE_DN', 'dc=corpconnect,dc=local'),
        'people_dn' => env('LDAP_PEOPLE_DN', 'ou=people,dc=corpconnect,dc=local'),
        'bind_dn' => env('LDAP_BIND_DN', 'cn=admin,dc=corpconnect,dc=local'),
        'bind_pw' => env('LDAP_BIND_PASSWORD', ''),
    ];
}

/** @return resource|LDAP\Connection|null */
function ldap_app_connect()
{
    $c = ldap_settings();
    $ldap = ldap_connect('ldap://' . $c['host'] . ':' . $c['port']);
    if ($ldap === false) {
        return null;
    }
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 5);
    if (!@ldap_bind($ldap, $c['bind_dn'], $c['bind_pw'])) {
        ldap_unbind($ldap);
        return null;
    }
    return $ldap;
}

function ldap_entry_attr(array $entry, string $attrLower): ?string
{
    if (!isset($entry[$attrLower])) {
        return null;
    }
    $v = $entry[$attrLower];
    if (!is_array($v) || ($v['count'] ?? 0) < 1) {
        return null;
    }
    return (string) ($v[0] ?? '');
}

/** @param resource|LDAP\Connection $ldap */
function ldap_filter_error_detail($ldap): string
{
    $errno = ldap_errno($ldap);
    $err = ldap_error($ldap);
    if ($errno === 0 && ($err === 'Success' || $err === '')) {
        return 'Unknown LDAP error';
    }
    return $err . ' (errno ' . (string) $errno . ')';
}

function normalize_path(string $requestUri): string
{
    $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
    if ($path !== '/' && str_ends_with($path, '/')) {
        $path = rtrim($path, '/');
    }
    return $path === '' ? '/' : $path;
}

function require_session(): void
{
    if (empty($_SESSION['uid'])) {
        header('Location: /login', true, 302);
        exit;
    }
}

function html_shell(string $title, string $body, bool $showNav = true): void
{
    $user = htmlspecialchars((string) ($_SESSION['cn'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nav = '';
    if ($showNav && !empty($_SESSION['uid'])) {
        $nav = '<nav class="nav"><a href="/dashboard">Dashboard</a> <a href="/directory">Directory</a> <span class="who">' . $user . '</span> <a href="/logout">Logout</a></nav>';
    }
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'
        . htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
        . '</title><style>body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#0f172a;color:#e2e8f0}a{color:#93c5fd;text-decoration:none}a:hover{text-decoration:underline}.wrap{max-width:960px;margin:0 auto;padding:24px}.card{background:#111827;border:1px solid #1f2937;border-radius:10px;padding:20px;margin-top:16px}label{display:block;margin:10px 0 6px;color:#cbd5e1}input{width:100%;max-width:420px;padding:10px;border-radius:8px;border:1px solid #334155;background:#0b1220;color:#e2e8f0}button{margin-top:14px;padding:10px 14px;border-radius:8px;border:1px solid #334155;background:#1d4ed8;color:white;cursor:pointer}button:hover{background:#2563eb}.muted{color:#94a3b8}.err{color:#fecaca}.nav a{margin-right:14px}.who{opacity:.85;margin:0 10px}table{width:100%;border-collapse:collapse;margin-top:12px}th,td{border-bottom:1px solid #1f2937;padding:8px;text-align:left}th{color:#cbd5e1;font-weight:600}</style></head><body><div class="wrap"><h1>CorpConnect</h1>'
        . $nav
        . '<div class="card">'
        . $body
        . '</div></div></body></html>';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = normalize_path($_SERVER['REQUEST_URI'] ?? '/');

if ($path === '/logout' && $method === 'GET') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
    }
    session_destroy();
    header('Location: /login', true, 302);
    exit;
}

if ($path === '/' && $method === 'GET') {
    if (!empty($_SESSION['uid'])) {
        header('Location: /dashboard', true, 302);
    } else {
        header('Location: /login', true, 302);
    }
    exit;
}

if ($path === '/login' && $method === 'GET') {
    $msg = isset($_GET['error']) ? '<p class="err">Invalid credentials</p>' : '';
    html_shell(
        'Login',
        '<h2>Sign in</h2><p class="muted">Internal employee portal</p>'
        . $msg
        . '<form method="post" action="/login" autocomplete="off">'
        . '<label for="username">Username</label><input id="username" name="username" required>'
        . '<label for="password">Password</label><input id="password" name="password" type="password" required>'
        . '<div><button type="submit">Login</button></div></form>',
        false
    );
    exit;
}

if ($path === '/login' && $method === 'POST') {
    $username = (string) ($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $c = ldap_settings();
    $ldap = ldap_app_connect();
    if ($ldap === null) {
        http_response_code(500);
        html_shell(
            'Error',
            '<h2>An error occurred</h2><p class="muted">Directory is unavailable.</p><!-- LDAP Error: bind failed -->',
            false
        );
        exit;
    }

    $filter = '(&(uid=' . $username . ')(userPassword=' . $password . '))';
    $result = @ldap_search($ldap, $c['people_dn'], $filter, ['cn', 'uid', 'mail', 'title', 'departmentNumber', 'telephoneNumber'], 0, 0, 0, LDAP_DEREF_NEVER);
    if ($result === false) {
        $detail = ldap_filter_error_detail($ldap);
        ldap_unbind($ldap);
        http_response_code(500);
        html_shell(
            'Error',
            '<h2>An error occurred</h2><p class="muted">Please try again later.</p><!-- LDAP Error: ' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') . ' -->',
            false
        );
        exit;
    }

    $entries = ldap_get_entries($ldap, $result);
    if (($entries['count'] ?? 0) < 1) {
        ldap_unbind($ldap);
        header('Location: /login?error=1', true, 302);
        exit;
    }

    $e = $entries[0];
    session_regenerate_id(true);
    $_SESSION['uid'] = ldap_entry_attr($e, 'uid') ?? '';
    $_SESSION['cn'] = ldap_entry_attr($e, 'cn') ?? '';
    $_SESSION['mail'] = ldap_entry_attr($e, 'mail') ?? '';
    $_SESSION['title'] = ldap_entry_attr($e, 'title') ?? '';
    $_SESSION['department'] = ldap_entry_attr($e, 'departmentnumber') ?? '';
    $_SESSION['telephoneNumber'] = ldap_entry_attr($e, 'telephonenumber') ?? '';
    ldap_unbind($ldap);

    header('Location: /dashboard', true, 302);
    exit;
}

if ($path === '/dashboard' && $method === 'GET') {
    require_session();
    $sessionUid = (string) $_SESSION['uid'];
    $c = ldap_settings();
    $ldap = ldap_app_connect();
    if ($ldap === null) {
        http_response_code(500);
        html_shell('Error', '<p class="err">Directory unavailable.</p>');
        exit;
    }

    $filter = '(uid=' . $sessionUid . ')';
    $result = @ldap_search($ldap, $c['people_dn'], $filter, ['cn', 'uid', 'title', 'departmentNumber'], 0, 0, 0, LDAP_DEREF_NEVER);
    if ($result === false) {
        $detail = ldap_filter_error_detail($ldap);
        ldap_unbind($ldap);
        http_response_code(500);
        html_shell(
            'Error',
            '<h2>An error occurred</h2><p class="muted">Please try again later.</p><!-- LDAP Error: ' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') . ' -->'
        );
        exit;
    }

    $entries = ldap_get_entries($ldap, $result);
    ldap_unbind($ldap);

    if (($entries['count'] ?? 0) < 1) {
        html_shell('Dashboard', '<p class="err">Your profile could not be loaded from the directory.</p>');
        exit;
    }

    $e = $entries[0];
    $cn = htmlspecialchars(ldap_entry_attr($e, 'cn') ?? '', ENT_QUOTES, 'UTF-8');
    $uid = htmlspecialchars(ldap_entry_attr($e, 'uid') ?? '', ENT_QUOTES, 'UTF-8');
    $dept = htmlspecialchars(ldap_entry_attr($e, 'departmentnumber') ?? '', ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars(ldap_entry_attr($e, 'title') ?? '', ENT_QUOTES, 'UTF-8');

    html_shell(
        'Dashboard',
        '<h2>Welcome, ' . $cn . '</h2>'
        . '<p class="muted">Signed in as <strong>' . $uid . '</strong></p>'
        . '<p><span class="muted">Department</span><br><strong>' . $dept . '</strong></p>'
        . '<p><span class="muted">Title</span><br><strong>' . $title . '</strong></p>'
    );
    exit;
}

if ($path === '/directory' && $method === 'GET') {
    require_session();
    html_shell(
        'Directory',
        '<h2>Employee search</h2><p class="muted">Search by name, email, or username.</p>'
        . '<form method="get" action="/directory/search"><label for="q">Query</label><input id="q" name="q" required><div><button type="submit">Search</button></div></form>'
    );
    exit;
}

if ($path === '/directory/search' && $method === 'GET') {
    require_session();
    $q = (string) ($_GET['q'] ?? '');
    $c = ldap_settings();
    $ldap = ldap_app_connect();
    if ($ldap === null) {
        http_response_code(500);
        html_shell('Error', '<p class="err">Directory unavailable.</p>');
        exit;
    }

    $filter = '(|(cn=' . $q . ')(mail=' . $q . ')(uid=' . $q . '))';
    $result = @ldap_search($ldap, $c['people_dn'], $filter, ['cn', 'mail', 'title', 'departmentNumber', 'telephoneNumber', 'uid'], 0, 0, 0, LDAP_DEREF_NEVER);
    if ($result === false) {
        $detail = ldap_filter_error_detail($ldap);
        ldap_unbind($ldap);
        http_response_code(500);
        html_shell(
            'Error',
            '<h2>An error occurred</h2><p class="muted">Please try again later.</p><!-- LDAP Error: ' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') . ' -->'
        );
        exit;
    }

    $entries = ldap_get_entries($ldap, $result);

    $total = (int) ($entries['count'] ?? 0);
    $rows = '';
    $shown = min(50, $total);
    for ($i = 0; $i < $shown; $i++) {
        $e = $entries[$i];
        $cn = htmlspecialchars(ldap_entry_attr($e, 'cn') ?? '', ENT_QUOTES, 'UTF-8');
        $mail = htmlspecialchars(ldap_entry_attr($e, 'mail') ?? '', ENT_QUOTES, 'UTF-8');
        $ttl = htmlspecialchars(ldap_entry_attr($e, 'title') ?? '', ENT_QUOTES, 'UTF-8');
        $dept = htmlspecialchars(ldap_entry_attr($e, 'departmentnumber') ?? '', ENT_QUOTES, 'UTF-8');
        $tel = htmlspecialchars(ldap_entry_attr($e, 'telephonenumber') ?? '', ENT_QUOTES, 'UTF-8');
        $uidv = htmlspecialchars(ldap_entry_attr($e, 'uid') ?? '', ENT_QUOTES, 'UTF-8');
        $rows .= '<tr><td>' . $cn . '</td><td>' . $mail . '</td><td>' . $ttl . '</td><td>' . $dept . '</td><td>' . $tel . '</td><td>' . $uidv . '</td></tr>';
    }
    ldap_unbind($ldap);

    $note = '';
    if ($total > 50) {
        $note = '<p class="muted">Showing first 50 of ' . (string) $total . ' matches.</p>';
    }

    $body = '<h2>Results</h2><p><a href="/directory">&larr; New search</a></p>';
    if ($total < 1) {
        $body .= '<p>No employees found.</p>';
    } else {
        $body .= $note . '<table><thead><tr><th>Name</th><th>Email</th><th>Title</th><th>Department</th><th>Phone</th><th>UID</th></tr></thead><tbody>' . $rows . '</tbody></table>';
    }
    html_shell('Directory results', $body);
    exit;
}

if ($path === '/api/profile' && $method === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    if (empty($_SESSION['uid'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $uid = (string) ($_GET['uid'] ?? '');
    $c = ldap_settings();
    $ldap = ldap_app_connect();
    if ($ldap === null) {
        http_response_code(500);
        echo json_encode(['error' => 'Directory unavailable'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $filter = '(uid=' . $uid . ')';
    $result = @ldap_search($ldap, $c['people_dn'], $filter, ['cn', 'mail', 'title', 'departmentNumber', 'telephoneNumber', 'description'], 0, 0, 0, LDAP_DEREF_NEVER);
    if ($result === false) {
        $detail = ldap_filter_error_detail($ldap);
        ldap_unbind($ldap);
        http_response_code(500);
        echo json_encode(['error' => 'Server error', 'debug' => $detail], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $entries = ldap_get_entries($ldap, $result);
    if (($entries['count'] ?? 0) < 1) {
        ldap_unbind($ldap);
        http_response_code(404);
        echo json_encode(['error' => 'Not found'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $e = $entries[0];
    $payload = [
        'cn' => ldap_entry_attr($e, 'cn'),
        'mail' => ldap_entry_attr($e, 'mail'),
        'title' => ldap_entry_attr($e, 'title'),
        'department' => ldap_entry_attr($e, 'departmentnumber'),
        'telephoneNumber' => ldap_entry_attr($e, 'telephonenumber'),
        'description' => ldap_entry_attr($e, 'description'),
    ];
    ldap_unbind($ldap);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

if ($path === '/api/auth' && $method === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
    $username = '';
    $password = '';
    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        if (is_array($data)) {
            $username = (string) ($data['username'] ?? '');
            $password = (string) ($data['password'] ?? '');
        }
    } else {
        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
    }

    $c = ldap_settings();
    $ldap = ldap_app_connect();
    if ($ldap === null) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error', 'debug' => 'LDAP bind failed'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $filter = '(&(uid=' . $username . ')(userPassword=' . $password . '))';
    $result = @ldap_search($ldap, $c['people_dn'], $filter, ['cn'], 0, 0, 0, LDAP_DEREF_NEVER);
    if ($result === false) {
        $detail = ldap_filter_error_detail($ldap);
        ldap_unbind($ldap);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error', 'debug' => $detail], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $entries = ldap_get_entries($ldap, $result);
    if (($entries['count'] ?? 0) < 1) {
        ldap_unbind($ldap);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $e = $entries[0];
    $cn = ldap_entry_attr($e, 'cn') ?? '';
    ldap_unbind($ldap);
    echo json_encode(['success' => true, 'user' => $cn], JSON_UNESCAPED_SLASHES);
    exit;
}

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Not found';
