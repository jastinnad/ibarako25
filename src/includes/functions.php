<?php
// Authentication and user management functions

function getCurrentUser() {
    return isset($_SESSION['current_user']) ? $_SESSION['current_user'] : null;
}

function handleLogin($email, $password, $role) {
    $users = $_SESSION['app_data']['users'];
    
    foreach ($users as $user) {
        if ($user['email'] === $email && $user['role'] === $role && $user['status'] === 'active') {
            // In a real app, verify password hash here
            $_SESSION['current_user'] = $user;
            return ['success' => true, 'message' => 'Login successful'];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid credentials'];
}

function handleSignup($userData) {
    // Check if email already exists
    $users = $_SESSION['app_data']['users'];
    foreach ($users as $user) {
        if ($user['email'] === $userData['email']) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
    }
    
    // Generate new member ID
    $memberNumbers = [];
    foreach ($users as $user) {
        if (isset($user['memberId'])) {
            $parts = explode('-', $user['memberId']);
            if (count($parts) === 2 && is_numeric($parts[1])) {
                $memberNumbers[] = intval($parts[1]);
            }
        }
    }
    
    $nextNumber = empty($memberNumbers) ? 1 : max($memberNumbers) + 1;
    $memberId = 'MBR-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    
    // Create new user with pending status
    $newUser = [
        'id' => 'user_' . time(),
        'memberId' => $memberId,
        'name' => $userData['name'],
        'email' => $userData['email'],
        'mobile' => $userData['mobile'],
        'address' => $userData['address'],
        'role' => 'member',
        'joinDate' => date('Y-m-d'),
        'status' => 'pending'
    ];
    
    // Create notification for admin
    $notification = [
        'id' => 'notif_' . time(),
        'type' => 'signup_request',
        'memberId' => $memberId,
        'memberName' => $userData['name'],
        'message' => 'New member signup request from ' . $userData['name'],
        'date' => date('Y-m-d'),
        'status' => 'pending',
        'data' => [
            'email' => $userData['email'],
            'mobile' => $userData['mobile'],
            'address' => $userData['address'],
            'password' => $userData['password'] // In real app, this should be hashed
        ]
    ];
    
    // Update session data
    $_SESSION['app_data']['users'][] = $newUser;
    $_SESSION['app_data']['notifications'][] = $notification;
    
    return [
        'success' => true,
        'message' => "Signup request submitted successfully! Your Member ID is {$memberId}. Please wait for admin approval.",
        'memberId' => $memberId
    ];
}

function handleLogout() {
    unset($_SESSION['current_user']);
    return ['success' => true, 'message' => 'Logged out successfully'];
}

function handleNotificationAction($notificationId, $action) {
    $notifications = &$_SESSION['app_data']['notifications'];
    $users = &$_SESSION['app_data']['users'];
    $loans = &$_SESSION['app_data']['loans'];
    
    // Find the notification
    $notificationIndex = -1;
    $notification = null;
    
    for ($i = 0; $i < count($notifications); $i++) {
        if ($notifications[$i]['id'] === $notificationId) {
            $notificationIndex = $i;
            $notification = $notifications[$i];
            break;
        }
    }
    
    if ($notificationIndex === -1) {
        return ['success' => false, 'message' => 'Notification not found'];
    }
    
    // Handle different notification types
    if ($notification['type'] === 'signup_request') {
        // Handle signup request
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['memberId'] === $notification['memberId']) {
                $users[$i]['status'] = $action === 'approve' ? 'active' : 'inactive';
                break;
            }
        }
    } elseif ($notification['type'] === 'profile_update' && $action === 'approve') {
        // Update user profile
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['memberId'] === $notification['memberId']) {
                $users[$i]['mobile'] = $notification['data']['mobile'];
                $users[$i]['email'] = $notification['data']['email'];
                break;
            }
        }
    } elseif ($notification['type'] === 'loan_application') {
        // Handle loan application
        for ($i = 0; $i < count($loans); $i++) {
            if ($loans[$i]['memberId'] === $notification['memberId'] && $loans[$i]['status'] === 'pending') {
                if ($action === 'approve') {
                    $loans[$i]['status'] = 'approved';
                    $loans[$i]['approvalDate'] = date('Y-m-d');
                } else {
                    $loans[$i]['status'] = 'rejected';
                }
                break;
            }
        }
    }
    
    // Update notification status
    $notifications[$notificationIndex]['status'] = $action === 'approve' ? 'approved' : 'rejected';
    
    $actionText = $notification['type'] === 'signup_request' ? 
        ($action === 'approve' ? 'approved and activated' : 'rejected') :
        $action . 'd';
        
    return [
        'success' => true,
        'message' => ucfirst(str_replace('_', ' ', $notification['type'])) . " {$actionText} for {$notification['memberName']}"
    ];
}

function getNotifications() {
    return $_SESSION['app_data']['notifications'];
}

function getPendingNotifications() {
    return array_filter($_SESSION['app_data']['notifications'], function($n) {
        return $n['status'] === 'pending';
    });
}

function formatDate($dateString) {
    return date('M j, Y', strtotime($dateString));
}

function formatCurrency($amount) {
    return '$' . number_format($amount);
}
?>