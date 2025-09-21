<?php
// This file is included in admin/dashboard.php
if (!isAdmin()) {
    exit('Access denied');
}

// Handle notification actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve_notification') {
        $notificationId = intval($_POST['notification_id'] ?? 0);
        $notificationData = json_decode($_POST['notification_data'] ?? '{}', true);
        $type = $_POST['notification_type'] ?? '';
        $memberId = $_POST['member_id'] ?? '';
        
        if ($notificationId > 0) {
            if (updateNotificationStatus($notificationId, 'approved')) {
                // Handle specific notification types
                if ($type === 'profile_update' && isset($notificationData['mobile']) && isset($notificationData['email'])) {
                    // Update member profile
                    $member = getUserByMemberId($memberId);
                    if ($member) {
                        $userData = [
                            'name' => $member['name'],
                            'email' => $notificationData['email'],
                            'mobile' => $notificationData['mobile'],
                            'address' => $member['address'],
                            'status' => $member['status']
                        ];
                        updateUser($member['id'], $userData);
                    }
                }
                showAlert('Request approved successfully', 'success');
            } else {
                showAlert('Failed to approve request', 'error');
            }
        }
    } elseif ($action === 'reject_notification') {
        $notificationId = intval($_POST['notification_id'] ?? 0);
        if ($notificationId > 0) {
            if (updateNotificationStatus($notificationId, 'rejected')) {
                showAlert('Request rejected', 'success');
            } else {
                showAlert('Failed to reject request', 'error');
            }
        }
    }
}

$notifications = getNotifications();
$pendingNotifications = array_filter($notifications, fn($n) => $n['status'] === 'pending');
$processedNotifications = array_filter($notifications, fn($n) => $n['status'] !== 'pending');

function getNotificationIcon($type) {
    $icons = [
        'loan_application' => 'fas fa-credit-card',
        'profile_update' => 'fas fa-user-edit',
        'contribution' => 'fas fa-dollar-sign'
    ];
    return $icons[$type] ?? 'fas fa-bell';
}

function getNotificationBadge($status) {
    $classes = [
        'pending' => 'badge-outline',
        'approved' => 'badge-default',
        'rejected' => 'badge-destructive'
    ];
    return 'badge ' . ($classes[$status] ?? 'badge-outline');
}
?>

<div class="container mx-auto" style="max-width: 1400px;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Notification Center</h2>
            <p class="text-muted-foreground">Review and process member requests and applications</p>
        </div>
        <div class="flex items-center gap-4">
            <?php if (count($pendingNotifications) > 0): ?>
                <span class="badge badge-destructive">
                    <?php echo count($pendingNotifications); ?> pending
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Pending Requests</h3>
                <i class="fas fa-clock text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($pendingNotifications); ?></div>
                <p class="text-sm text-muted-foreground">Awaiting approval</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Processed Today</h3>
                <i class="fas fa-check-circle text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <?php 
                $todayProcessed = array_filter($processedNotifications, fn($n) => $n['date'] === date('Y-m-d'));
                ?>
                <div class="text-2xl font-bold"><?php echo count($todayProcessed); ?></div>
                <p class="text-sm text-muted-foreground">Completed today</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Loan Applications</h3>
                <i class="fas fa-credit-card text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <?php 
                $loanNotifications = array_filter($notifications, fn($n) => $n['type'] === 'loan_application');
                ?>
                <div class="text-2xl font-bold"><?php echo count($loanNotifications); ?></div>
                <p class="text-sm text-muted-foreground">Total applications</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Profile Updates</h3>
                <i class="fas fa-user-edit text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <?php 
                $profileNotifications = array_filter($notifications, fn($n) => $n['type'] === 'profile_update');
                ?>
                <div class="text-2xl font-bold"><?php echo count($profileNotifications); ?></div>
                <p class="text-sm text-muted-foreground">Update requests</p>
            </div>
        </div>
    </div>

    <!-- Pending Notifications -->
    <?php if (!empty($pendingNotifications)): ?>
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="card-title">Pending Requests</h3>
                <p class="card-description">Review and process member requests requiring approval</p>
            </div>
            <div class="card-content">
                <div class="grid gap-4">
                    <?php foreach ($pendingNotifications as $notification): 
                        $data = json_decode($notification['data'] ?? '{}', true);
                    ?>
                        <div class="p-4 border border-border rounded">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-4 flex-1">
                                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="<?php echo getNotificationIcon($notification['type']); ?> text-muted-foreground"></i>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-medium"><?php echo htmlspecialchars($notification['member_name']); ?></h4>
                                            <span class="text-sm text-muted-foreground"><?php echo htmlspecialchars($notification['member_id']); ?></span>
                                            <span class="badge badge-outline"><?php echo ucfirst(str_replace('_', ' ', $notification['type'])); ?></span>
                                        </div>
                                        
                                        <p class="text-sm mb-3"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        
                                        <?php if ($notification['type'] === 'profile_update' && is_array($data)): ?>
                                            <div class="grid grid-cols-2 gap-4 p-3 bg-muted rounded text-sm">
                                                <?php if (isset($data['mobile'])): ?>
                                                    <div>
                                                        <span class="font-medium">New Mobile:</span> <?php echo htmlspecialchars($data['mobile']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (isset($data['email'])): ?>
                                                    <div>
                                                        <span class="font-medium">New Email:</span> <?php echo htmlspecialchars($data['email']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif ($notification['type'] === 'loan_application' && is_array($data)): ?>
                                            <div class="grid grid-cols-2 gap-4 p-3 bg-muted rounded text-sm">
                                                <?php if (isset($data['amount'])): ?>
                                                    <div>
                                                        <span class="font-medium">Amount:</span> <?php echo formatCurrency($data['amount']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (isset($data['termMonths'])): ?>
                                                    <div>
                                                        <span class="font-medium">Term:</span> <?php echo $data['termMonths']; ?> months
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="text-xs text-muted-foreground mt-2">
                                            <?php echo formatDate($notification['date']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="approve_notification">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                        <input type="hidden" name="notification_data" value="<?php echo htmlspecialchars($notification['data'] ?? '{}'); ?>">
                                        <input type="hidden" name="notification_type" value="<?php echo $notification['type']; ?>">
                                        <input type="hidden" name="member_id" value="<?php echo $notification['member_id']; ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-check mr-2"></i>
                                            Approve
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="reject_notification">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                        <button type="submit" class="btn btn-destructive btn-sm" onclick="return confirm('Are you sure you want to reject this request?')">
                                            <i class="fas fa-times mr-2"></i>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card mb-6">
            <div class="card-content">
                <div class="text-center py-8">
                    <i class="fas fa-bell-slash text-muted-foreground mb-4" style="font-size: 3rem;"></i>
                    <h3 class="text-lg font-medium mb-2">No pending requests</h3>
                    <p class="text-muted-foreground">
                        All member requests have been processed
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- All Notifications History -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Notification History</h3>
            <p class="card-description">Complete history of all notifications and their status</p>
        </div>
        <div class="card-content">
            <?php if (!empty($notifications)): ?>
                <div class="overflow-auto">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Member</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Sort notifications by date (newest first)
                            usort($notifications, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                            
                            foreach ($notifications as $notification): ?>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <i class="<?php echo getNotificationIcon($notification['type']); ?> text-muted-foreground"></i>
                                            <span class="text-sm"><?php echo ucfirst(str_replace('_', ' ', $notification['type'])); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($notification['member_name']); ?></div>
                                            <div class="text-sm text-muted-foreground"><?php echo htmlspecialchars($notification['member_id']); ?></div>
                                        </div>
                                    </td>
                                    <td class="text-sm"><?php echo htmlspecialchars($notification['message']); ?></td>
                                    <td><?php echo formatDate($notification['date']); ?></td>
                                    <td>
                                        <span class="<?php echo getNotificationBadge($notification['status']); ?>">
                                            <?php echo ucfirst($notification['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-bell text-muted-foreground mb-4" style="font-size: 3rem;"></i>
                    <h3 class="text-lg font-medium mb-2">No notifications yet</h3>
                    <p class="text-muted-foreground">
                        Member requests and applications will appear here
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>