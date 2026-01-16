<?php

// Test authentication for Pattern Approval users
require 'vendor/autoload.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$email = 'kenedymoshi21@gmail.com'; // User ID 8
$password = 'Kene@2118'; // Actual password used during registration

echo "Testing authentication for: $email\n";
echo "Password: $password\n\n";

$auth = service('auth');
$credentials = [
    'email' => $email,
    'password' => $password
];

try {
    $result = $auth->attempt($credentials);
    
    if ($result) {
        echo "✓ Authentication SUCCESSFUL!\n";
        $user = $auth->user();
        echo "User ID: {$user->id}\n";
        echo "Username: {$user->username}\n";
        echo "Email: {$user->email}\n";
    } else {
        echo "✗ Authentication FAILED!\n";
        echo "Invalid credentials\n";
    }
} catch (\Exception $e) {
    echo "✗ Authentication ERROR!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
