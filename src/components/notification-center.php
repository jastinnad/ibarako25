<?php
$notifications = getNotifications();
$pendingNotifications = getPendingNotifications();
?>

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Notification Center</h2>
            <p class="text-muted-foreground">Manage member requests and applications</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 border border-border rounded-md text-sm">
                <?= count($pendingNotifications) ?> pending
            </span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid md:grid-cols-5 gap-4">
        <div class="bg-white border border-border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Total Notifications</h3>
                <i data-lucide="bell" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($notifications) ?></div>
            <p class="text-xs text-muted-foreground">All notifications</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Pending</h3>
                <i data-lucide="alert-circle" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($pendingNotifications) ?></div>
            <p class="text-xs text-muted-foreground">Awaiting action</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Signup Requests</h3>
                <i data-lucide="user-plus" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold">
                <?= count(array_filter($notifications, fn($n) => $n['type'] === 'signup_request')) ?>
            </div>
            <p class="text-xs text-muted-foreground">New members</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Loan Applications</h3>
                <i data-lucide="credit-card" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold">
                <?= count(array_filter($notifications, fn($n) => $n['type'] === 'loan_application')) ?>
            </div>
            <p class="text-xs text-muted-foreground">Loan requests</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Profile Updates</h3>
                <i data-lucide="user" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold">
                <?= count(array_filter($notifications, fn($n) => $n['type'] === 'profile_update')) ?>
            </div>
            <p class="text-xs text-muted-foreground">Update requests</p>
        </div>
    </div>

    <!-- Pending Notifications -->
    <?php if (count($pendingNotifications) > 0): ?>
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>Pending Notifications</span>
            </h3>
            <p class="text-sm text-muted-foreground">Notifications requiring your immediate attention</p>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($pendingNotifications as $notification): ?>
            <div class="flex items-center justify-between p-4 border border-border rounded-lg">
                <div class="flex items-start space-x-4">
                    <div class="p-2 rounded-lg <?= getNotificationColor($notification['type']) ?>">
                        <i data-lucide="<?= getNotificationIcon($notification['type']) ?>" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <p class="font-medium"><?= htmlspecialchars($notification['memberName']) ?></p>
                            <span class="px-2 py-1 bg-muted text-muted-foreground text-xs rounded border">
                                <?= htmlspecialchars($notification['memberId']) ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($notification['message']) ?></p>
                        <?php if (isset($notification['data'])): ?>
                        <div class="text-xs text-muted-foreground">
                            <?php if ($notification['type'] === 'signup_request'): ?>
                                Email: <?= htmlspecialchars($notification['data']['email']) ?> | 
                                Mobile: <?= htmlspecialchars($notification['data']['mobile']) ?>
                            <?php elseif ($notification['type'] === 'profile_update'): ?>
                                New Mobile: <?= htmlspecialchars($notification['data']['mobile']) ?> | 
                                New Email: <?= htmlspecialchars($notification['data']['email']) ?>
                            <?php elseif ($notification['type'] === 'loan_application'): ?>
                                Amount: <?= formatCurrency($notification['data']['amount']) ?> | 
                                Term: <?= $notification['data']['termMonths'] ?> months
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <p class="text-xs text-muted-foreground mt-1">
                            <?= formatDate($notification['date']) ?>
                        </p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button 
                        onclick="handleNotification('<?= $notification['id'] ?>', 'approve')"
                        class="bg-primary text-primary-foreground px-3 py-2 rounded-md text-sm hover:bg-primary/90 transition-colors flex items-center space-x-1"
                    >
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Approve</span>
                    </button>
                    <button 
                        onclick="handleNotification('<?= $notification['id'] ?>', 'reject')"
                        class="border border-border text-foreground px-3 py-2 rounded-md text-sm hover:bg-muted transition-colors flex items-center space-x-1"
                    >
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        <span>Reject</span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- All Notifications History -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Notification History</h3>
            <p class="text-sm text-muted-foreground">Complete history of all member notifications</p>
        </div>
        <div class="p-6">
            <?php if (count($notifications) === 0): ?>
            <div class="text-center py-8">
                <i data-lucide="bell" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                <h3 class="text-lg font-medium mb-2">No notifications</h3>
                <p class="text-muted-foreground">All member notifications will appear here</p>
            </div>
            <?php else: ?>
            <div class="space-y-3">
                <?php 
                // Sort notifications by date (newest first)
                usort($notifications, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                foreach ($notifications as $notification): 
                ?>
                <div class="flex items-center justify-between p-3 border border-border rounded-lg">
                    <div class="flex items-start space-x-3">
                        <div class="p-2 rounded-lg <?= getNotificationColor($notification['type']) ?>">
                            <i data-lucide="<?= getNotificationIcon($notification['type']) ?>" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <p class="font-medium text-sm"><?= htmlspecialchars($notification['memberName']) ?></p>
                                <span class="px-2 py-1 bg-muted text-muted-foreground text-xs rounded border">
                                    <?= htmlspecialchars($notification['memberId']) ?>
                                </span>
                                <span class="px-2 py-1 bg-muted text-muted-foreground text-xs rounded border">
                                    <?= str_replace('_', ' ', $notification['type']) ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                            <p class="text-xs text-muted-foreground">
                                <?= formatDate($notification['date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs rounded border <?= getStatusBadgeClass($notification['status']) ?>">
                            <?= ucfirst($notification['status']) ?>
                        </span>
                        <?php if ($notification['status'] === 'pending'): ?>
                        <div class="flex space-x-1">
                            <button 
                                onclick="handleNotification('<?= $notification['id'] ?>', 'approve')"
                                class="border border-border p-1 rounded-md hover:bg-muted transition-colors"
                            >
                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                            </button>
                            <button 
                                onclick="handleNotification('<?= $notification['id'] ?>', 'reject')"
                                class="border border-border p-1 rounded-md hover:bg-muted transition-colors"
                            >
                                <i data-lucide="x-circle" class="w-3 h-3"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function handleNotification(notificationId, action) {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${action}_notification&notificationId=${encodeURIComponent(notificationId)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('An error occurred', 'error');
    });
}
</script>

<?php
function getNotificationIcon($type) {
    switch ($type) {
        case 'loan_application':
            return 'credit-card';
        case 'profile_update':
            return 'user';
        case 'contribution':
            return 'dollar-sign';
        case 'signup_request':
            return 'user-plus';
        default:
            return 'bell';
    }
}

function getNotificationColor($type) {
    switch ($type) {
        case 'loan_application':
            return 'bg-blue-100 text-blue-600';
        case 'profile_update':
            return 'bg-green-100 text-green-600';
        case 'contribution':
            return 'bg-purple-100 text-purple-600';
        case 'signup_request':
            return 'bg-orange-100 text-orange-600';
        default:
            return 'bg-gray-100 text-gray-600';
    }
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'border-orange-200 text-orange-600 bg-orange-50';
        case 'approved':
            return 'border-green-200 text-green-600 bg-green-50';
        case 'rejected':
            return 'border-red-200 text-red-600 bg-red-50';
        default:
            return 'border-gray-200 text-gray-600 bg-gray-50';
    }
}
?>