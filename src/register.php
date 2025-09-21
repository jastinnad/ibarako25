<?php
session_start();
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($mobile) || empty($address) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (getUserByEmail($email)) {
        $error = 'Email address already exists';
    } else {
        // Create user
        $userData = [
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'address' => $address,
            'password' => $password,
            'role' => 'member'
        ];
        
        if (createUser($userData)) {
            $success = 'Account created successfully! Please contact admin for activation.';
        } else {
            $error = 'Failed to create account. Please try again.';
        }
    }
}

// If successful, redirect to login page
if ($success) {
    $_SESSION['alert'] = ['message' => $success, 'type' => 'success'];
    header('Location: login.php');
    exit();
}

// If error, redirect back to login page with error
if ($error) {
    $_SESSION['alert'] = ['message' => $error, 'type' => 'error'];
    header('Location: login.php');
    exit();
}

// If no POST data, redirect to login
header('Location: login.php');
exit();
?>