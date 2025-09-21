<?php
// This file is included in member/dashboard.php
if (!isset($user)) {
    exit('Access denied');
}

// Get member loans
$loans = getLoansByMember($user['member_id']);
$interestRate = getInterestRate();

// Handle loan application
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'apply_loan') {
    $amount = floatval($_POST['amount'] ?? 0);
    $termMonths = intval($_POST['term_months'] ?? 6);
    
    if ($amount <= 0) {
        showAlert('Please enter a valid amount', 'error');
    } else {
        $loanData = [
            'member_id' => $user['member_id'],
            'amount' => $amount,
            'term_months' => $termMonths,
            'interest_rate' => $interestRate,
            'status' => 'pending'
        ];
        
        if (createLoan($loanData)) {
            // Create notification for admin
            $notificationData = [
                'type' => 'loan_application',
                'member_id' => $user['member_id'],
                'member_name' => $user['name'],
                'message' => "New loan application for " . formatCurrency($amount),
                'data' => ['amount' => $amount, 'termMonths' => $termMonths]
            ];
            createNotification($notificationData);
            
            showAlert('Loan application submitted successfully', 'success');
            // Refresh loans
            $loans = getLoansByMember($user['member_id']);
        } else {
            showAlert('Failed to submit loan application', 'error');
        }
    }
}

$activeLoans = array_filter($loans, fn($l) => $l['status'] === 'active');
$pendingLoans = array_filter($loans, fn($l) => $l['status'] === 'pending');
$totalBorrowed = array_sum(array_column($activeLoans, 'amount'));

function getStatusBadge($status) {
    $classes = [
        'pending' => 'badge-outline',
        'approved' => 'badge-secondary',
        'active' => 'badge-default',
        'completed' => 'badge-secondary',
        'rejected' => 'badge-destructive'
    ];
    return 'badge ' . ($classes[$status] ?? 'badge-outline');
}

function calculateProgress($loan) {
    return $loan['total_payments'] > 0 ? ($loan['payments_made'] / $loan['total_payments']) * 100 : 0;
}
?>

<div class="container mx-auto" style="max-width: 1400px;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Loans</h2>
            <p class="text-muted-foreground">Manage your loan applications and payments</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('agreementModal')" class="btn btn-outline">
                <i class="fas fa-file-contract mr-2"></i>
                View Agreement
            </button>
            <button onclick="openModal('applyLoanModal')" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Apply for Loan
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Active Loans</h3>
                <i class="fas fa-credit-card text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($activeLoans); ?></div>
                <p class="text-sm text-muted-foreground">
                    Total borrowed: <?php echo formatCurrency($totalBorrowed); ?>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Pending Applications</h3>
                <i class="fas fa-clock text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($pendingLoans); ?></div>
                <p class="text-sm text-muted-foreground">
                    Awaiting approval
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Interest Rate</h3>
                <i class="fas fa-percentage text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo $interestRate; ?>%</div>
                <p class="text-sm text-muted-foreground">
                    Annual rate (<?php echo $interestRate/2; ?>% per payment)
                </p>
            </div>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Loan History</h3>
            <p class="card-description">
                All your loan applications and current loans
            </p>
        </div>
        <div class="card-content">
            <?php if (empty($loans)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-credit-card text-muted-foreground mb-4" style="font-size: 3rem;"></i>
                    <h3 class="text-lg font-medium mb-2">No loans yet</h3>
                    <p class="text-muted-foreground mb-4">
                        Apply for your first loan to get started
                    </p>
                    <button onclick="openModal('applyLoanModal')" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Apply for Loan
                    </button>
                </div>
            <?php else: ?>
                <div class="overflow-auto">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Application Date</th>
                                <th>Amount</th>
                                <th>Term</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Sort loans by application date (newest first)
                            usort($loans, fn($a, $b) => strtotime($b['application_date']) - strtotime($a['application_date']));
                            
                            foreach ($loans as $loan): ?>
                                <tr>
                                    <td><?php echo formatDate($loan['application_date']); ?></td>
                                    <td class="font-medium"><?php echo formatCurrency($loan['amount']); ?></td>
                                    <td><?php echo $loan['term_months']; ?> months</td>
                                    <td>
                                        <span class="<?php echo getStatusBadge($loan['status']); ?>">
                                            <?php echo ucfirst($loan['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($loan['status'] === 'active'): ?>
                                            <div class="grid gap-1">
                                                <div class="flex justify-between text-sm">
                                                    <span><?php echo $loan['payments_made']; ?>/<?php echo $loan['total_payments']; ?></span>
                                                    <span><?php echo round(calculateProgress($loan)); ?>%</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: <?php echo calculateProgress($loan); ?>%"></div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted-foreground">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (in_array($loan['status'], ['active', 'approved'])): ?>
                                            <button onclick="viewLoanAgreement(<?php echo htmlspecialchars(json_encode($loan)); ?>)" class="btn btn-outline btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Apply Loan Modal -->
<div class="modal" id="applyLoanModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Apply for New Loan</h3>
            <p class="modal-description">
                Fill out the loan application form. Current interest rate: <?php echo $interestRate; ?>% annually
            </p>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="apply_loan">
            
            <div class="form-group">
                <label class="label" for="loan-amount">Loan Amount</label>
                <input class="input" id="loanAmount" name="amount" type="number" step="0.01" min="100" placeholder="Enter loan amount" required onchange="calculateLoanPayments()">
            </div>
            
            <div class="form-group">
                <label class="label" for="term-months">Loan Term</label>
                <select class="select" id="termMonths" name="term_months" required onchange="calculateLoanPayments()">
                    <option value="3">3 Months</option>
                    <option value="6" selected>6 Months</option>
                    <option value="12">12 Months</option>
                    <option value="18">18 Months</option>
                    <option value="24">24 Months</option>
                </select>
            </div>
            
            <div class="p-4 bg-accent rounded">
                <h4 class="font-medium mb-2">Payment Information</h4>
                <p class="text-sm text-muted-foreground mb-2">
                    • Payments are made twice monthly (15th and 30th)
                </p>
                <p class="text-sm text-muted-foreground mb-2">
                    • Interest rate: <?php echo $interestRate; ?>% annually (<?php echo $interestRate/2; ?>% per payment)
                </p>
                <p class="text-sm text-muted-foreground">
                    • Total payments: <span id="totalPayments">12</span>
                </p>
                <p class="text-sm text-muted-foreground">
                    • Payment amount: <span id="paymentAmount">$0.00</span>
                </p>
            </div>
            
            <input type="hidden" id="interestRate" value="<?php echo $interestRate; ?>">
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal('applyLoanModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
</div>

<!-- Loan Agreement Modal -->
<div class="modal" id="agreementModal">
    <div class="modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3 class="modal-title">Loan Agreement</h3>
            <p class="modal-description">Standard Loan Agreement Template</p>
            <button onclick="printPage()" class="btn btn-outline btn-sm ml-auto">
                <i class="fas fa-print mr-2"></i>
                Print Agreement
            </button>
        </div>
        <div id="agreementContent">
            <?php include 'loan-agreement.php'; ?>
        </div>
    </div>
</div>

<script>
function viewLoanAgreement(loan) {
    // Update agreement content with loan details
    openModal('agreementModal');
}

// Calculate loan payments when form values change
document.addEventListener('DOMContentLoaded', function() {
    calculateLoanPayments();
});
</script>