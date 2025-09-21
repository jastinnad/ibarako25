<?php
// This file is included in admin/dashboard.php
if (!isAdmin()) {
    exit('Access denied');
}

// Handle loan actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve_loan') {
        $loanId = intval($_POST['loan_id'] ?? 0);
        if ($loanId > 0) {
            if (updateLoanStatus($loanId, 'approved')) {
                showAlert('Loan approved successfully', 'success');
            } else {
                showAlert('Failed to approve loan', 'error');
            }
        }
    } elseif ($action === 'reject_loan') {
        $loanId = intval($_POST['loan_id'] ?? 0);
        if ($loanId > 0) {
            if (updateLoanStatus($loanId, 'rejected')) {
                showAlert('Loan rejected', 'success');
            } else {
                showAlert('Failed to reject loan', 'error');
            }
        }
    } elseif ($action === 'activate_loan') {
        $loanId = intval($_POST['loan_id'] ?? 0);
        $startDate = $_POST['start_date'] ?? date('Y-m-d');
        if ($loanId > 0) {
            $additionalData = [
                'start_date' => $startDate,
                'next_payment_date' => date('Y-m-d', strtotime($startDate . ' +15 days'))
            ];
            if (updateLoanStatus($loanId, 'active', $additionalData)) {
                showAlert('Loan activated successfully', 'success');
            } else {
                showAlert('Failed to activate loan', 'error');
            }
        }
    }
}

$loans = getAllLoans();
$pendingLoans = array_filter($loans, fn($l) => $l['status'] === 'pending');
$approvedLoans = array_filter($loans, fn($l) => $l['status'] === 'approved');
$activeLoans = array_filter($loans, fn($l) => $l['status'] === 'active');
$completedLoans = array_filter($loans, fn($l) => $l['status'] === 'completed');

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

<div class="container mx-auto" style="max-width: 1600px;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Loan Management</h2>
            <p class="text-muted-foreground">Approve, manage, and monitor all member loans</p>
        </div>
        <div class="text-sm text-muted-foreground">
            Total loans: <?php echo count($loans); ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Pending Approval</h3>
                <i class="fas fa-clock text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($pendingLoans); ?></div>
                <p class="text-sm text-muted-foreground">
                    <?php echo formatCurrency(array_sum(array_column($pendingLoans, 'amount'))); ?> total
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Active Loans</h3>
                <i class="fas fa-credit-card text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($activeLoans); ?></div>
                <p class="text-sm text-muted-foreground">
                    <?php echo formatCurrency(array_sum(array_column($activeLoans, 'amount'))); ?> outstanding
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Completed Loans</h3>
                <i class="fas fa-check-circle text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($completedLoans); ?></div>
                <p class="text-sm text-muted-foreground">
                    <?php echo formatCurrency(array_sum(array_column($completedLoans, 'amount'))); ?> total paid
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Approved (Pending Start)</h3>
                <i class="fas fa-play-circle text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($approvedLoans); ?></div>
                <p class="text-sm text-muted-foreground">
                    Ready to activate
                </p>
            </div>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Loans</h3>
            <p class="card-description">Complete overview of all member loans and their status</p>
        </div>
        <div class="card-content">
            <div class="overflow-auto">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Term</th>
                            <th>Application Date</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Next Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Sort loans by application date (newest first)
                        usort($loans, fn($a, $b) => strtotime($b['application_date']) - strtotime($a['application_date']));
                        
                        foreach ($loans as $loan): ?>
                            <tr>
                                <td>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($loan['member_name']); ?></div>
                                        <div class="text-sm text-muted-foreground"><?php echo htmlspecialchars($loan['member_id']); ?></div>
                                    </div>
                                </td>
                                <td class="font-medium"><?php echo formatCurrency($loan['amount']); ?></td>
                                <td><?php echo $loan['term_months']; ?> months</td>
                                <td><?php echo formatDate($loan['application_date']); ?></td>
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
                                    <?php if ($loan['next_payment_date']): ?>
                                        <?php echo formatDate($loan['next_payment_date']); ?>
                                    <?php else: ?>
                                        <span class="text-muted-foreground">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <?php if ($loan['status'] === 'pending'): ?>
                                            <button onclick="approveLoan(<?php echo $loan['id']; ?>)" class="btn btn-secondary btn-sm" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="rejectLoan(<?php echo $loan['id']; ?>)" class="btn btn-destructive btn-sm" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php elseif ($loan['status'] === 'approved'): ?>
                                            <button onclick="activateLoan(<?php echo $loan['id']; ?>)" class="btn btn-primary btn-sm" title="Activate">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button onclick="viewLoanDetails(<?php echo htmlspecialchars(json_encode($loan)); ?>)" class="btn btn-outline btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
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
</div>

<!-- Loan Details Modal -->
<div class="modal" id="loanDetailsModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Loan Details</h3>
            <p class="modal-description">Complete loan information and payment schedule</p>
        </div>
        <div id="loanDetailsContent">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('loanDetailsModal')" class="btn btn-outline">Close</button>
        </div>
    </div>
</div>

<!-- Activate Loan Modal -->
<div class="modal" id="activateLoanModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Activate Loan</h3>
            <p class="modal-description">Set the loan start date to activate the loan</p>
        </div>
        <form method="POST" id="activateLoanForm">
            <input type="hidden" name="action" value="activate_loan">
            <input type="hidden" name="loan_id" id="activate_loan_id">
            
            <div class="form-group">
                <label class="label" for="start_date">Start Date</label>
                <input class="input" id="start_date" name="start_date" type="date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="p-3 bg-accent rounded">
                <p class="text-sm">
                    <strong>Note:</strong> The first payment will be due 15 days after the start date.
                </p>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal('activateLoanModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Activate Loan</button>
            </div>
        </form>
    </div>
</div>

<script>
function approveLoan(loanId) {
    if (confirm('Are you sure you want to approve this loan?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="approve_loan">
            <input type="hidden" name="loan_id" value="${loanId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectLoan(loanId) {
    if (confirm('Are you sure you want to reject this loan?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="reject_loan">
            <input type="hidden" name="loan_id" value="${loanId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function activateLoan(loanId) {
    document.getElementById('activate_loan_id').value = loanId;
    openModal('activateLoanModal');
}

function viewLoanDetails(loan) {
    const content = document.getElementById('loanDetailsContent');
    const progress = loan.total_payments > 0 ? (loan.payments_made / loan.total_payments) * 100 : 0;
    
    content.innerHTML = `
        <div class="grid gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Member</label>
                    <p class="font-medium">${loan.member_name} (${loan.member_id})</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Loan Amount</label>
                    <p class="font-medium">$${parseFloat(loan.amount).toLocaleString()}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Term</label>
                    <p>${loan.term_months} months</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Interest Rate</label>
                    <p>${loan.interest_rate}% annually</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Payment Amount</label>
                    <p class="font-medium">$${parseFloat(loan.monthly_payment).toLocaleString()}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Application Date</label>
                    <p>${new Date(loan.application_date).toLocaleDateString()}</p>
                </div>
            </div>
            
            ${loan.status === 'active' ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Payment Progress</label>
                    <div class="mt-2">
                        <div class="flex justify-between text-sm mb-1">
                            <span>${loan.payments_made}/${loan.total_payments} payments</span>
                            <span>${Math.round(progress)}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: ${progress}%"></div>
                        </div>
                    </div>
                </div>
            ` : ''}
            
            ${loan.next_payment_date ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Next Payment Due</label>
                    <p>${new Date(loan.next_payment_date).toLocaleDateString()}</p>
                </div>
            ` : ''}
        </div>
    `;
    
    openModal('loanDetailsModal');
}
</script>