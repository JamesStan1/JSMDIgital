<?php
/**
 * JSM Digital — Staff User Seed Script
 *
 * Run this ONCE from the command line to create the initial admin account:
 *
 *   php backend/create-admin.php
 *
 * It will prompt for a username, email, full name, and password
 * interactively, then insert the account into the database.
 *
 * You can also pass arguments non-interactively:
 *
 *   php backend/create-admin.php admin admin@jsmdigital.com "Admin Name" "S3cur3P@ss!" admin
 *
 * Usage: php create-admin.php [username] [email] [full_name] [password] [role]
 * Roles: admin | manager | staff
 */

// Only allow CLI execution
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo json_encode(['error' => 'This script can only be run from the command line.']);
    exit(1);
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "\n=== JSM Digital — Create Staff User ===\n\n";

// Helper to read CLI input
function prompt(string $label, bool $hidden = false): string
{
    echo $label . ': ';
    if ($hidden && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        system('stty -echo');
    }
    $val = rtrim(fgets(STDIN));
    if ($hidden && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        system('stty echo');
        echo "\n";
    }
    return $val;
}

// Gather inputs (args or interactive)
$username  = $argv[1] ?? prompt('Username');
$email     = $argv[2] ?? prompt('Email');
$full_name = $argv[3] ?? prompt('Full Name');
$password  = $argv[4] ?? prompt('Password', hidden: true);
$role      = $argv[5] ?? prompt('Role (admin/manager/staff) [admin]');

if ($role === '') $role = 'admin';

// Validate
$errors = [];
if ($username === '')                            $errors[] = 'Username is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if ($full_name === '')                           $errors[] = 'Full name is required.';
if (strlen($password) < 8)                      $errors[] = 'Password must be at least 8 characters.';
if (!in_array($role, ['admin', 'manager', 'staff'], true)) $errors[] = "Role must be admin, manager, or staff.";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $e) echo "  - $e\n";
    exit(1);
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    $pdo  = getDbConnection();

    // Ensure tables exist
    // (tables will be auto-created next time the API is hit; here we create manually for this script)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS staff_users (
            id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username   VARCHAR(100) NOT NULL UNIQUE,
            email      VARCHAR(320) NOT NULL UNIQUE,
            password   VARCHAR(255) NOT NULL,
            full_name  VARCHAR(255) NOT NULL,
            role       ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff',
            is_active  TINYINT(1)   NOT NULL DEFAULT 1,
            created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $stmt = $pdo->prepare(
        'INSERT INTO staff_users (username, email, password, full_name, role)
         VALUES (:u, :e, :p, :f, :r)'
    );
    $stmt->execute([
        ':u' => $username,
        ':e' => $email,
        ':p' => $hash,
        ':f' => $full_name,
        ':r' => $role,
    ]);

    echo "\n✓ Staff user created successfully!\n";
    echo "  Username : $username\n";
    echo "  Email    : $email\n";
    echo "  Role     : $role\n\n";
    echo "You can now log in at /login\n\n";
} catch (\PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate entry')) {
        echo "\n✗ A user with that username or email already exists.\n\n";
    } else {
        echo "\n✗ Database error: " . $e->getMessage() . "\n\n";
    }
    exit(1);
}
