<?php
require_once __DIR__ . '/../includes/functions.php';
ensureSession();

// Auth guard: only allow if logged in and admin
$currentUser = getCurrentUser();
if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
    setAlert('You must be an admin to access the dashboard', 'destructive');
    redirect('/bbloan/src/login.php');
}

$notifications = getNotifications();
$pendingNotifications = getPendingNotifications();
$users = $_SESSION['app_data']['users'] ?? [];
$loans = $_SESSION['app_data']['loans'] ?? [];

$pageTitle = 'Admin Dashboard - Loan Management System';
include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-background">
    <!-- Header -->
    <header class="bg-white border-b border-border">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="piggy-bank" class="h-8 w-8 text-primary"></i>
                    <h1 class="text-xl font-semibold">LoanSystem Admin</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-muted-foreground">Welcome, <?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></span>
                    <button onclick="logout()" class="bg-secondary text-secondary-foreground px-4 py-2 rounded-md hover:bg-secondary/80 transition-colors">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white border-b border-border">
        <div class="container mx-auto px-4">
            <div class="flex space-x-8">
                <button onclick="showTab('overview')" id="overviewTab" class="py-4 px-2 border-b-2 border-primary text-primary font-medium">
                    Overview
                </button>
                <button onclick="showTab('notifications')" id="notificationsTab" class="py-4 px-2 border-b-2 border-transparent text-muted-foreground hover:text-foreground">
                    Notifications 
                    <?php if (count($pendingNotifications) > 0): ?>
                        <span class="ml-1 bg-destructive text-destructive-foreground text-xs px-2 py-1 rounded-full">
                            <?= count($pendingNotifications) ?>
                        </span>
                    <?php endif; ?>
                </button>
                <button onclick="showTab('members')" id="membersTab" class="py-4 px-2 border-b-2 border-transparent text-muted-foreground hover:text-foreground">
                    Members
                </button>
                <button onclick="showTab('loans')" id="loansTab" class="py-4 px-2 border-b-2 border-transparent text-muted-foreground hover:text-foreground">
                    Loans
                </button>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Overview Tab -->
        <div id="overviewContent" class="tab-content">
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <!-- Summary Cards -->
                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Total Members</h3>
                        <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= count(array_filter($users, fn($u) => $u['role'] === 'member')) ?></div>
                    <p class="text-xs text-muted-foreground">Active members</p>
                </div>

                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Active Loans</h3>
                        <i data-lucide="credit-card" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= count(array_filter($loans, fn($l) => $l['status'] === 'active')) ?></div>
                    <p class="text-xs text-muted-foreground">Currently active</p>
                </div>

                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Pending Applications</h3>
                        <i data-lucide="clock" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= count(array_filter($loans, fn($l) => $l['status'] === 'pending')) ?></div>
                    <p class="text-xs text-muted-foreground">Awaiting approval</p>
                </div>

                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Total Notifications</h3>
                        <i data-lucide="bell" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= count($notifications) ?></div>
                    <p class="text-xs text-muted-foreground">All notifications</p>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div id="notificationsContent" class="tab-content hidden">
            <?php include __DIR__ . '/../components/notification-center.php'; ?>
        </div>

        <!-- Members Tab -->
        <div id="membersContent" class="tab-content hidden">
            <?php include __DIR__ . '/../components/member-management.php'; ?>
        </div>

        <!-- Loans Tab -->
        <div id="loansContent" class="tab-content hidden">
            <?php include __DIR__ . '/../components/loan-management.php'; ?>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('nav button').forEach(tab => {
        tab.classList.remove('border-primary', 'text-primary');
        tab.classList.add('border-transparent', 'text-muted-foreground');
    });
    
    // Show selected tab content
    document.getElementById(tabName + 'Content').classList.remove('hidden');
    
    // Add active state to selected tab
    const activeTab = document.getElementById(tabName + 'Tab');
    activeTab.classList.remove('border-transparent', 'text-muted-foreground');
    activeTab.classList.add('border-primary', 'text-primary');
}

function logout() {
    fetch('/bbloan/src/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=logout'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/bbloan/src/login.php';
        }
    });
}

// Initialize lucide icons if available
document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide && window.lucide.createIcons) {
    window.lucide.createIcons();
  }
});
</script>
<?php // End of page ?>