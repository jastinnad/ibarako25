<?php
session_start();
require_once '../includes/functions.php';

requireAdmin();

$user = getUserById($_SESSION['user_id']);
$activeTab = $_GET['tab'] ?? 'members';

// Get pending counts for badges
$pendingNotifications = count(getNotifications('pending'));
$pendingLoans = count(array_filter(getAllLoans(), fn($l) => $l['status'] === 'pending'));

$pageTitle = 'Admin Dashboard - Loan Management System';
include '../includes/header.php';
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar open" id="sidebar">
        <div class="sidebar-header">
            <div class="flex items-center gap-2">
                <div style="width: 2rem; height: 2rem; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <p class="font-medium">Admin Panel</p>
                    <p class="text-sm text-muted-foreground"><?php echo htmlspecialchars($user['name']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="?tab=members" class="sidebar-menu-button <?php echo $activeTab === 'members' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div>Members</div>
                                    <div class="text-sm text-muted-foreground"><?php echo count(getAllMembers()); ?> members</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?tab=loans" class="sidebar-menu-button <?php echo $activeTab === 'loans' ? 'active' : ''; ?>">
                        <i class="fas fa-credit-card"></i>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div>Loans</div>
                                    <div class="text-sm text-muted-foreground"><?php echo $pendingLoans; ?> pending</div>
                                </div>
                                <?php if ($pendingLoans > 0): ?>
                                    <span class="badge badge-destructive"><?php echo $pendingLoans; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?tab=notifications" class="sidebar-menu-button <?php echo $activeTab === 'notifications' ? 'active' : ''; ?>">
                        <i class="fas fa-bell"></i>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div>Notifications</div>
                                    <div class="text-sm text-muted-foreground"><?php echo $pendingNotifications; ?> pending</div>
                                </div>
                                <?php if ($pendingNotifications > 0): ?>
                                    <span class="badge badge-destructive"><?php echo $pendingNotifications; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?tab=settings" class="sidebar-menu-button <?php echo $activeTab === 'settings' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <div>
                            <div>Settings</div>
                            <div class="text-sm text-muted-foreground">System configuration</div>
                        </div>
                    </a>
                </li>
            </ul>
            
            <div class="mt-auto pt-4 border-t">
                <a href="../logout.php" class="sidebar-menu-button w-full">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <header class="header">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="btn btn-outline" style="display: none;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-xl font-semibold">
                        <?php
                        $titles = [
                            'members' => 'Members',
                            'loans' => 'Loans',
                            'notifications' => 'Notifications',
                            'settings' => 'Settings'
                        ];
                        echo ($titles[$activeTab] ?? 'Dashboard') . ' Management';
                        ?>
                    </h1>
                </div>
                <div class="flex items-center gap-4">
                    <?php if ($pendingNotifications > 0): ?>
                        <a href="?tab=notifications" class="btn btn-outline btn-sm relative">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-destructive absolute" style="top: -8px; right: -8px; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; padding: 0; font-size: 10px;">
                                <?php echo $pendingNotifications; ?>
                            </span>
                        </a>
                    <?php endif; ?>
                    <div class="text-sm text-muted-foreground">
                        Administrator Dashboard
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            <?php
            switch ($activeTab) {
                case 'members':
                    include 'members.php';
                    break;
                case 'loans':
                    include 'loans.php';
                    break;
                case 'notifications':
                    include 'notifications.php';
                    break;
                case 'settings':
                    include 'settings.php';
                    break;
                default:
                    include 'members.php';
            }
            ?>
        </main>
    </div>
</div>

<style>
@media (max-width: 1024px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .header button {
        display: block !important;
    }
}
</style>

<?php include '../includes/footer.php'; ?>