<?php
// This file is included in admin/dashboard.php
if (!isAdmin()) {
    exit('Access denied');
}

// Handle settings update
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_settings') {
    $interestRate = floatval($_POST['interest_rate'] ?? 2.0);
    
    if ($interestRate >= 0 && $interestRate <= 100) {
        if (updateSystemSetting('interest_rate', $interestRate)) {
            showAlert('Settings updated successfully', 'success');
        } else {
            showAlert('Failed to update settings', 'error');
        }
    } else {
        showAlert('Invalid interest rate', 'error');
    }
}

$currentInterestRate = getInterestRate();
$allMembers = getAllMembers();
$allLoans = getAllLoans();
$allContributions = [];

// Get all contributions
foreach ($allMembers as $member) {
    $memberContributions = getContributionsByMember($member['member_id']);
    $allContributions = array_merge($allContributions, $memberContributions);
}

// Calculate statistics
$totalMembers = count($allMembers);
$activeMembers = count(array_filter($allMembers, fn($m) => $m['status'] === 'active'));
$totalLoans = count($allLoans);
$activeLoans = count(array_filter($allLoans, fn($l) => $l['status'] === 'active'));
$pendingLoans = count(array_filter($allLoans, fn($l) => $l['status'] === 'pending'));
$totalLoanAmount = array_sum(array_column($allLoans, 'amount'));
$activeLoanAmount = array_sum(array_column(array_filter($allLoans, fn($l) => $l['status'] === 'active'), 'amount'));
$totalContributions = array_sum(array_column($allContributions, 'amount'));

// Recent activity
$recentLoans = array_slice(array_filter($allLoans, fn($l) => strtotime($l['application_date']) > strtotime('-30 days')), 0, 5);
$recentContributions = array_slice(array_filter($allContributions, fn($c) => strtotime($c['date']) > strtotime('-30 days')), 0, 5);
?>

<div class="container mx-auto" style="max-width: 1400px;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">System Settings</h2>
            <p class="text-muted-foreground">Configure system parameters and view statistics</p>
        </div>
        <div class="text-sm text-muted-foreground">
            System Administrator
        </div>
    </div>

    <!-- Settings Card -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title">Interest Rate Configuration</h3>
            <p class="card-description">Manage the annual interest rate for new loans</p>
        </div>
        <div class="card-content">
            <form method="POST" class="grid gap-4" style="max-width: 400px;">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="form-group">
                    <label class="label" for="interest_rate">Annual Interest Rate (%)</label>
                    <input 
                        class="input" 
                        id="interest_rate" 
                        name="interest_rate" 
                        type="number" 
                        step="0.1" 
                        min="0" 
                        max="100" 
                        value="<?php echo $currentInterestRate; ?>" 
                        required
                    >
                    <p class="text-sm text-muted-foreground mt-1">
                        This rate will be applied to all new loan applications. Current rate: <?php echo $currentInterestRate; ?>%
                    </p>
                </div>
                
                <div class="p-3 bg-accent rounded">
                    <h4 class="font-medium mb-2">Payment Calculation</h4>
                    <p class="text-sm text-muted-foreground">
                        • Payments are made twice monthly (15th and 30th)<br>
                        • Each payment includes <?php echo $currentInterestRate/2; ?>% interest + principal<br>
                        • Interest is split equally between the two monthly payments
                    </p>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Interest Rate</button>
            </form>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="grid grid-cols-1 gap-6 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Member Statistics</h3>
            </div>
            <div class="card-content">
                <div class="grid gap-4">
                    <div class="flex justify-between">
                        <span>Total Members:</span>
                        <span class="font-medium"><?php echo $totalMembers; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Active Members:</span>
                        <span class="font-medium"><?php echo $activeMembers; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Inactive Members:</span>
                        <span class="font-medium"><?php echo $totalMembers - $activeMembers; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Contributions:</span>
                        <span class="font-medium"><?php echo formatCurrency($totalContributions); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Loan Statistics</h3>
            </div>
            <div class="card-content">
                <div class="grid gap-4">
                    <div class="flex justify-between">
                        <span>Total Loans:</span>
                        <span class="font-medium"><?php echo $totalLoans; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Active Loans:</span>
                        <span class="font-medium"><?php echo $activeLoans; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pending Approval:</span>
                        <span class="font-medium"><?php echo $pendingLoans; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Loan Value:</span>
                        <span class="font-medium"><?php echo formatCurrency($totalLoanAmount); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Active Loan Value:</span>
                        <span class="font-medium"><?php echo formatCurrency($activeLoanAmount); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Health</h3>
            </div>
            <div class="card-content">
                <div class="grid gap-4">
                    <div class="flex justify-between">
                        <span>Database Status:</span>
                        <span class="badge badge-default">Connected</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Current Interest Rate:</span>
                        <span class="font-medium"><?php echo $currentInterestRate; ?>%</span>
                    </div>
                    <div class="flex justify-between">
                        <span>System Uptime:</span>
                        <span class="badge badge-secondary">Online</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Last Backup:</span>
                        <span class="text-sm text-muted-foreground">Manual</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Loan Applications</h3>
                <p class="card-description">Last 30 days</p>
            </div>
            <div class="card-content">
                <?php if (!empty($recentLoans)): ?>
                    <div class="grid gap-3">
                        <?php foreach ($recentLoans as $loan): ?>
                            <div class="flex justify-between items-center p-3 bg-muted rounded">
                                <div>
                                    <div class="font-medium"><?php echo htmlspecialchars($loan['member_name']); ?></div>
                                    <div class="text-sm text-muted-foreground">
                                        <?php echo formatCurrency($loan['amount']); ?> • <?php echo formatDate($loan['application_date']); ?>
                                    </div>
                                </div>
                                <span class="badge <?php echo $loan['status'] === 'pending' ? 'badge-outline' : 'badge-default'; ?>">
                                    <?php echo ucfirst($loan['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="text-muted-foreground">No recent loan applications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Contributions</h3>
                <p class="card-description">Last 30 days</p>
            </div>
            <div class="card-content">
                <?php if (!empty($recentContributions)): ?>
                    <div class="grid gap-3">
                        <?php 
                        // Sort recent contributions by date
                        usort($recentContributions, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                        $recentContributions = array_slice($recentContributions, 0, 5);
                        
                        foreach ($recentContributions as $contribution): 
                            $member = getUserByMemberId($contribution['member_id']);
                        ?>
                            <div class="flex justify-between items-center p-3 bg-muted rounded">
                                <div>
                                    <div class="font-medium"><?php echo htmlspecialchars($member['name'] ?? 'Unknown'); ?></div>
                                    <div class="text-sm text-muted-foreground">
                                        <?php echo formatCurrency($contribution['amount']); ?> • <?php echo formatDate($contribution['date']); ?>
                                    </div>
                                </div>
                                <span class="badge <?php echo $contribution['type'] === 'monthly' ? 'badge-default' : 'badge-secondary'; ?>">
                                    <?php echo ucfirst($contribution['type']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="text-muted-foreground">No recent contributions</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="card-title">System Information</h3>
            <p class="card-description">Technical details and configuration</p>
        </div>
        <div class="card-content">
            <div class="grid grid-cols-1 gap-4" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <div>
                    <h4 class="font-medium mb-2">Database</h4>
                    <div class="text-sm space-y-1">
                        <div>Engine: MySQL</div>
                        <div>Tables: 5 (users, loans, contributions, notifications, settings)</div>
                        <div>Status: Connected</div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium mb-2">Application</h4>
                    <div class="text-sm space-y-1">
                        <div>Version: 1.0.0</div>
                        <div>Framework: PHP Native</div>
                        <div>Environment: <?php echo $_SERVER['SERVER_NAME'] ?? 'Development'; ?></div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium mb-2">Security</h4>
                    <div class="text-sm space-y-1">
                        <div>Password Hashing: bcrypt</div>
                        <div>Session Security: Enabled</div>
                        <div>CSRF Protection: Basic</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>