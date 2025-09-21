<?php
$loans = $_SESSION['app_data']['loans'];
$users = $_SESSION['app_data']['users'];

// Create a lookup for member names
$memberNames = [];
foreach ($users as $user) {
    if (isset($user['memberId'])) {
        $memberNames[$user['memberId']] = $user['name'];
    }
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-semibold">Loan Management</h2>
        <p class="text-muted-foreground">Manage all loan applications and active loans</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid md:grid-cols-4 gap-6">
        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Total Loans</h3>
                <i data-lucide="credit-card" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($loans) ?></div>
            <p class="text-xs text-muted-foreground">All loans</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Active Loans</h3>
                <i data-lucide="trending-up" class="h-4 w-4 text-muted-foreground"></i>
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
                <h3 class="text-sm font-medium text-muted-foreground">Total Value</h3>
                <i data-lucide="dollar-sign" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold">
                <?= formatCurrency(array_sum(array_column(array_filter($loans, fn($l) => $l['status'] === 'active'), 'amount'))) ?>
            </div>
            <p class="text-xs text-muted-foreground">Active loan value</p>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">All Loans</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Member
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Term
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Application Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($loans as $loan): ?>
                    <tr class="hover:bg-muted/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary">
                                            <?php
                                            $memberName = $memberNames[$loan['memberId']] ?? 'Unknown';
                                            echo strtoupper(substr($memberName, 0, 2));
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-foreground">
                                        <?= htmlspecialchars($memberName) ?>
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        <?= htmlspecialchars($loan['memberId']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-foreground">
                                <?= formatCurrency($loan['amount']) ?>
                            </div>
                            <div class="text-sm text-muted-foreground">
                                <?= formatCurrency($loan['monthlyPayment']) ?>/month
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
                            <?= $loan['termMonths'] ?> months
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($loan['status'] === 'active'): ?>
                            <div class="text-sm text-foreground">
                                <?= $loan['paymentsMade'] ?>/<?= $loan['totalPayments'] ?>
                            </div>
                            <div class="w-full bg-muted rounded-full h-2 mt-1">
                                <div 
                                    class="bg-primary h-2 rounded-full" 
                                    style="width: <?= ($loan['paymentsMade'] / $loan['totalPayments']) * 100 ?>%"
                                ></div>
                            </div>
                            <?php else: ?>
                            <span class="text-sm text-muted-foreground">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded border <?= getLoanStatusClass($loan['status']) ?>">
                                <?= ucfirst($loan['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            <?= formatDate($loan['applicationDate']) ?>
                            <?php if (isset($loan['approvalDate'])): ?>
                            <div class="text-xs">
                                Approved: <?= formatDate($loan['approvalDate']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <button class="text-primary hover:text-primary/80" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <?php if ($loan['status'] === 'pending'): ?>
                                <button 
                                    onclick="approveLoan('<?= $loan['id'] ?>')"
                                    class="text-green-600 hover:text-green-800"
                                    title="Approve"
                                >
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                                <button 
                                    onclick="rejectLoan('<?= $loan['id'] ?>')"
                                    class="text-red-600 hover:text-red-800"
                                    title="Reject"
                                >
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                                <button class="text-muted-foreground hover:text-foreground" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function approveLoan(loanId) {
    // This would be implemented to approve a loan
    showToast('Loan approval functionality would be implemented here', 'info');
}

function rejectLoan(loanId) {
    // This would be implemented to reject a loan
    showToast('Loan rejection functionality would be implemented here', 'info');
}
</script>

<?php
function getLoanStatusClass($status) {
    switch ($status) {
        case 'active':
            return 'border-green-200 text-green-600 bg-green-50';
        case 'approved':
            return 'border-blue-200 text-blue-600 bg-blue-50';
        case 'pending':
            return 'border-orange-200 text-orange-600 bg-orange-50';
        case 'rejected':
            return 'border-red-200 text-red-600 bg-red-50';
        case 'completed':
            return 'border-gray-200 text-gray-600 bg-gray-50';
        default:
            return 'border-gray-200 text-gray-600 bg-gray-50';
    }
}
?>