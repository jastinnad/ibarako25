<?php
$contributions = array_filter($_SESSION['app_data']['contributions'], fn($c) => $c['memberId'] === $currentUser['memberId']);
$loans = array_filter($_SESSION['app_data']['loans'], fn($l) => $l['memberId'] === $currentUser['memberId']);
$totalContributions = array_sum(array_column($contributions, 'amount'));
$activeLoans = array_filter($loans, fn($l) => $l['status'] === 'active');
?>

<div class="min-h-screen bg-background">
    <!-- Header -->
    <header class="bg-white border-b border-border">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="piggy-bank" class="h-8 w-8 text-primary"></i>
                    <h1 class="text-xl font-semibold">LoanSystem</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium"><?= htmlspecialchars($currentUser['name']) ?></p>
                        <p class="text-xs text-muted-foreground"><?= htmlspecialchars($currentUser['memberId']) ?></p>
                    </div>
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
                <button onclick="showTab('dashboard')" id="dashboardTab" class="py-4 px-2 border-b-2 border-primary text-primary font-medium">
                    Dashboard
                </button>
                <button onclick="showTab('profile')" id="profileTab" class="py-4 px-2 border-b-2 border-transparent text-muted-foreground hover:text-foreground">
                    Profile
                </button>
                <button onclick="showTab('contributions')" id="contributionsTab" class="py-4 px-2 border-b-2 border-transparent text-muted-foreground hover:text-foreground">
                    Contributions
                </button>
                <button onclick="showTab('loans')" id="loansTab" class="py-4 px-2 border-b-2 border-transparent text-muted-foreground hover:text-foreground">
                    Loans
                </button>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Dashboard Tab -->
        <div id="dashboardContent" class="tab-content">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-2">Welcome back, <?= htmlspecialchars($currentUser['name']) ?>!</h2>
                <p class="text-muted-foreground">Here's an overview of your account activity.</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Total Contributions</h3>
                        <i data-lucide="piggy-bank" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= formatCurrency($totalContributions) ?></div>
                    <p class="text-xs text-muted-foreground">Total saved</p>
                </div>

                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Active Loans</h3>
                        <i data-lucide="credit-card" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= count($activeLoans) ?></div>
                    <p class="text-xs text-muted-foreground">Currently active</p>
                </div>

                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Member Since</h3>
                        <i data-lucide="calendar" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= date('Y', strtotime($currentUser['joinDate'])) ?></div>
                    <p class="text-xs text-muted-foreground"><?= formatDate($currentUser['joinDate']) ?></p>
                </div>

                <div class="bg-white border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Account Status</h3>
                        <i data-lucide="check-circle" class="h-4 w-4 text-muted-foreground"></i>
                    </div>
                    <div class="text-2xl font-bold"><?= ucfirst($currentUser['status']) ?></div>
                    <p class="text-xs text-muted-foreground">Account status</p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Recent Contributions -->
                <div class="bg-white border border-border rounded-lg">
                    <div class="p-6 border-b border-border">
                        <h3 class="text-lg font-semibold">Recent Contributions</h3>
                    </div>
                    <div class="p-6">
                        <?php if (count($contributions) > 0): ?>
                        <div class="space-y-4">
                            <?php 
                            $recentContributions = array_slice(array_reverse($contributions), 0, 3);
                            foreach ($recentContributions as $contribution): 
                            ?>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium"><?= htmlspecialchars($contribution['description']) ?></p>
                                    <p class="text-xs text-muted-foreground"><?= formatDate($contribution['date']) ?></p>
                                </div>
                                <span class="text-sm font-medium text-green-600">
                                    +<?= formatCurrency($contribution['amount']) ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted-foreground py-4">No contributions yet</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Active Loans -->
                <div class="bg-white border border-border rounded-lg">
                    <div class="p-6 border-b border-border">
                        <h3 class="text-lg font-semibold">Active Loans</h3>
                    </div>
                    <div class="p-6">
                        <?php if (count($activeLoans) > 0): ?>
                        <div class="space-y-4">
                            <?php foreach ($activeLoans as $loan): ?>
                            <div class="border border-border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium"><?= formatCurrency($loan['amount']) ?> Loan</h4>
                                    <span class="text-sm text-muted-foreground"><?= $loan['paymentsMade'] ?>/<?= $loan['totalPayments'] ?></span>
                                </div>
                                <div class="w-full bg-muted rounded-full h-2 mb-2">
                                    <div 
                                        class="bg-primary h-2 rounded-full" 
                                        style="width: <?= ($loan['paymentsMade'] / $loan['totalPayments']) * 100 ?>%"
                                    ></div>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    Next payment: <?= isset($loan['nextPaymentDate']) ? formatDate($loan['nextPaymentDate']) : 'N/A' ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted-foreground py-4">No active loans</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Tab -->
        <div id="profileContent" class="tab-content hidden">
            <?php include 'components/member-profile.php'; ?>
        </div>

        <!-- Contributions Tab -->
        <div id="contributionsContent" class="tab-content hidden">
            <?php include 'components/member-contributions.php'; ?>
        </div>

        <!-- Loans Tab -->
        <div id="loansContent" class="tab-content hidden">
            <?php include 'components/member-loans.php'; ?>
        </div>
    </main>
</div>

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
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=logout'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>