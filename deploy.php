<?php
/**
 * Auto-Deployment Script for ByetHost
 *
 * Usage: Access this file via browser or cron job
 * Example: https://yourdomain.com/deploy.php?secret=YOUR_SECRET_TOKEN
 *
 * For automatic GitHub deployment:
 * 1. Set up a webhook in GitHub to point to this script
 * 2. Or use a service like DeployHQ/DeployBot
 */

// Configuration
$config = [
    'github_secret' => getenv('DEPLOY_SECRET') ?: 'your-secret-token-here',
    'allowed_ips' => [
        '140.82.112.0/20',  // GitHub
        '::1',              // Localhost
    ],
    'git_repo' => 'your-username/suivitravaux',
    'git_branch' => 'main',
];

// Response helper
function respond($code, $message, $data = null) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $code === 200 ? 'success' : 'error',
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Check if secret token is provided
$secret = $_GET['secret'] ?? $_POST['secret'] ?? null;

if ($secret !== $config['github_secret']) {
    respond(401, 'Unauthorized: Invalid or missing secret token');
}

// Verify GitHub IP (optional security)
$client_ip = $_SERVER['REMOTE_ADDR'];
$ip_allowed = false;
foreach ($config['allowed_ips'] as $range) {
    if (strpos($range, '/') !== false) {
        // CIDR notation
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $ip_allowed = (ip2long($client_ip) & $mask) == ($ip & $mask);
    } else {
        $ip_allowed = $client_ip === $range;
    }
    if ($ip_allowed) break;
}

// Only check IP for GitHub webhook (skip for manual trigger)
if (!$ip_allowed && empty($_GET['manual'])) {
    // Log but continue (for flexibility)
    error_log("Deploy from unknown IP: $client_ip");
}

// Change to project directory
$projectRoot = __DIR__;
chdir($projectRoot);

// Commands to run
$commands = [
    'echo "Starting deployment..."',
    'git fetch --all',
    'git reset --hard origin/' . $config['git_branch'],
    'echo "Git pull completed"',
    'cp -n .env.example .env 2>/dev/null || true',
    'composer install --no-dev --optimize-autoloader',
    'php artisan migrate --force',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache',
    'chmod -R 755 storage/',
    'chmod -R 755 bootstrap/cache/',
    'php artisan cache:clear',
    'php artisan optimize:clear',
    'echo "Deployment completed!"',
];

$output = [];
$returnVar = 0;

// Execute commands
foreach ($commands as $cmd) {
    echo "Executing: $cmd\n";
    exec("$cmd 2>&1", $output, $returnVar);

    if ($returnVar !== 0) {
        respond(500, "Command failed: $cmd", [
            'output' => implode("\n", $output),
            'return_code' => $returnVar
        ]);
    }
}

respond(200, 'Deployment completed successfully!', [
    'commands_executed' => count($commands),
    'output' => implode("\n", $output)
]);

