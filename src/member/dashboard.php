<?php
session_start();
require_once '../includes/functions.php';

requireMember();

$user = getUserById($_SESSION['user_id']);
$activeTab = $_GET['tab'] ?? 'profile';

$pageTitle = 'Member Dashboard - Loan Management System';
include '../includes/header.php';
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="flex items-center gap-2">
                <div style="width: 2rem; height: 2rem; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="font-medium"><?php echo htmlspecialchars($user['name']); ?></p>
                    <p class="text-sm text-muted-foreground"><?php echo htmlspecialchars($user['member_id']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="?tab=profile" class="sidebar-menu-button <?php echo $activeTab === 'profile' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i>
                        <div>
                            <div>Profile</div>
                            <div class="text-sm text-muted-foreground">View profile information</div>
                        </div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?tab=contributions" class="sidebar-menu-button <?php echo $activeTab === 'contributions' ? 'active' : ''; ?>">
                        <i class="fas fa-dollar-sign"></i>
                        <div>
                            <div>Contributions</div>
                            <div class="text-sm text-muted-foreground">Manage contributions</div>
                        </div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="?tab=loans" class="sidebar-menu-button <?php echo $activeTab === 'loans' ? 'active' : ''; ?>">
                        <i class="fas fa-credit-card"></i>
                        <div>
                            <div>Loans</div>
                            <div class="text-sm text-muted-foreground">View and apply for loans</div>
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
                            'profile' => 'Profile',
                            'contributions' => 'Contributions', 
                            'loans' => 'Loans'
                        ];
                        echo $titles[$activeTab] ?? 'Dashboard';
                        ?> Dashboard
                    </h1>
                </div>
                <div class="text-sm text-muted-foreground">
                    Welcome back, <?php echo htmlspecialchars($user['name']); ?>
                </div>
            </div>
        </header>

        <main class="p-6">
            <?php
            switch ($activeTab) {
                case 'profile':
                    include 'profile.php';
                    break;
                case 'contributions':
                    include 'contributions.php';
                    break;
                case 'loans':
                    include 'loans.php';
                    break;
                default:
                    include 'profile.php';
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