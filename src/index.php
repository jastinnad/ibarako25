<?php
session_start();

// Include database and functions
require_once 'config/database.php';
require_once 'includes/functions.php';

// Initialize sample data if not exists
if (!isset($_SESSION['app_data'])) {
    $_SESSION['app_data'] = [
        'users' => [
            [
                'id' => 'admin1',
                'name' => 'System Administrator',
                'email' => 'admin@loan.com',
                'mobile' => '+1234567890',
                'address' => '123 Admin St',
                'role' => 'admin',
                'joinDate' => '2024-01-01',
                'status' => 'active'
            ],
            [
                'id' => 'user1',
                'memberId' => 'MBR-0001',
                'name' => 'John Doe',
                'email' => 'john@email.com',
                'mobile' => '+1234567891',
                'address' => '456 Member St',
                'role' => 'member',
                'joinDate' => '2024-01-15',
                'status' => 'active'
            ],
            [
                'id' => 'user2',
                'memberId' => 'MBR-0002',
                'name' => 'Jane Smith',
                'email' => 'jane@email.com',
                'mobile' => '+1234567892',
                'address' => '789 Member Ave',
                'role' => 'member',
                'joinDate' => '2024-02-01',
                'status' => 'active'
            ]
        ],
        'contributions' => [
            [
                'id' => 'cont1',
                'memberId' => 'MBR-0001',
                'amount' => 500,
                'date' => '2024-01-15',
                'type' => 'monthly',
                'description' => 'Monthly contribution'
            ],
            [
                'id' => 'cont2',
                'memberId' => 'MBR-0001',
                'amount' => 200,
                'date' => '2024-01-20',
                'type' => 'additional',
                'description' => 'Additional savings'
            ]
        ],
        'loans' => [
            [
                'id' => 'loan1',
                'memberId' => 'MBR-0001',
                'amount' => 10000,
                'termMonths' => 6,
                'interestRate' => 2,
                'status' => 'active',
                'applicationDate' => '2024-01-20',
                'approvalDate' => '2024-01-22',
                'startDate' => '2024-02-01',
                'monthlyPayment' => 1700,
                'paymentsMade' => 4,
                'totalPayments' => 12,
                'nextPaymentDate' => '2024-04-15',
                'agreementSigned' => true
            ]
        ],
        'notifications' => [
            [
                'id' => 'notif1',
                'type' => 'loan_application',
                'memberId' => 'MBR-0002',
                'memberName' => 'Jane Smith',
                'message' => 'New loan application for $15,000',
                'date' => '2024-03-15',
                'status' => 'pending',
                'data' => [
                    'amount' => 15000,
                    'termMonths' => 12
                ]
            ]
        ],
        'interestRate' => 2
    ];
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'login':
            echo json_encode(handleLogin($_POST['email'], $_POST['password'], $_POST['role']));
            exit;
            
        case 'signup':
            echo json_encode(handleSignup($_POST));
            exit;
            
        case 'logout':
            echo json_encode(handleLogout());
            exit;
            
        case 'approve_notification':
            echo json_encode(handleNotificationAction($_POST['notificationId'], 'approve'));
            exit;
            
        case 'reject_notification':
            echo json_encode(handleNotificationAction($_POST['notificationId'], 'reject'));
            exit;
    }
}

// Check if user is logged in
$currentUser = getCurrentUser();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoanSystem - Loan Management System</title>
    <link rel="stylesheet" href="styles/globals.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="min-h-screen bg-background">
    <div id="app">
        <?php if (!$currentUser): ?>
            <?php include 'pages/landing.php'; ?>
        <?php elseif ($currentUser['role'] === 'admin'): ?>
            <?php include 'pages/admin-dashboard.php'; ?>
        <?php else: ?>
            <?php include 'pages/member-dashboard.php'; ?>
        <?php endif; ?>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/toast.js"></script>
</body>
</html>