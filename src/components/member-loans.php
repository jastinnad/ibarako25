<?php
$loans = array_filter($_SESSION['app_data']['loans'], fn($l) => $l['memberId'] === $currentUser['memberId']);
$activeLoans = array_filter($loans, fn($l) => $l['status'] === 'active');
$pendingLoans = array_filter($loans, fn($l) => $l['status'] === 'pending');
$totalBorrowed = array_sum(array_column($activeLoans, 'amount'));
?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-semibold">My Loans</h2>
        <p class="text-muted-foreground">Manage your loan applications and active loans</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid md:grid-cols-4 gap-6">
        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Total Borrowed</h3>
                <i data-lucide="credit-card" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= formatCurrency($totalBorrowed) ?></div>
            <p class="text-xs text-muted-foreground">Active loans</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Active Loans</h3>
                <i data-lucide="trending-up" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($activeLoans) ?></div>
            <p class="text-xs text-muted-foreground">Currently active</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Pending Applications</h3>
                <i data-lucide="clock" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($pendingLoans) ?></div>
            <p class="text-xs text-muted-foreground">Awaiting approval</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Interest Rate</h3>
                <i data-lucide="percent" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= $_SESSION['app_data']['interestRate'] ?>%</div>
            <p class="text-xs text-muted-foreground">Annual rate</p>
        </div>
    </div>

    <!-- Apply for New Loan -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Apply for New Loan</h3>
            <p class="text-sm text-muted-foreground">Submit a new loan application</p>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Loan Information</h4>
                        <ul class="text-sm text-blue-700 mt-1 space-y-1">
                            <li>• Interest rate: <?= $_SESSION['app_data']['interestRate'] ?>% annual (bi-monthly payments)</li>
                            <li>• Payments due on 15th and 30th of each month</li>
                            <li>• All loans require admin approval</li>
                            <li>• Loan agreement must be signed before disbursement</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="loanApplicationForm" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="loanAmount" class="text-sm font-medium">Loan Amount</label>
                        <input 
                            id="loanAmount" 
                            type="number" 
                            step="100" 
                            min="1000" 
                            max="50000" 
                            placeholder="10000" 
                            class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                            required
                        >
                        <p class="text-xs text-muted-foreground">Minimum: $1,000 | Maximum: $50,000</p>
                    </div>

                    <div class="space-y-2">
                        <label for="termMonths" class="text-sm font-medium">Loan Term (Months)</label>
                        <select 
                            id="termMonths" 
                            class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                            required
                        >
                            <option value="">Select term</option>
                            <option value="6">6 months</option>
                            <option value="12">12 months</option>
                            <option value="18">18 months</option>
                            <option value="24">24 months</option>
                            <option value="36">36 months</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="loanPurpose" class="text-sm font-medium">Purpose of Loan</label>
                    <textarea 
                        id="loanPurpose" 
                        rows="3" 
                        placeholder="Please describe what you will use this loan for" 
                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                        required
                    ></textarea>
                </div>

                <!-- Loan Calculation Preview -->
                <div id="loanPreview" class="hidden bg-muted/50 rounded-lg p-4">
                    <h4 class="font-medium mb-2">Loan Preview</h4>
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-muted-foreground">Monthly Payment:</span>
                            <div id="monthlyPayment" class="font-medium"></div>
                        </div>
                        <div>
                            <span class="text-muted-foreground">Total Payments:</span>
                            <div id="totalPayments" class="font-medium"></div>
                        </div>
                        <div>
                            <span class="text-muted-foreground">Total Interest:</span>
                            <div id="totalInterest" class="font-medium"></div>
                        </div>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="bg-primary text-primary-foreground px-6 py-2 rounded-md hover:bg-primary/90 transition-colors"
                >
                    Submit Application
                </button>
            </form>
        </div>
    </div>

    <!-- Active Loans -->
    <?php if (count($activeLoans) > 0): ?>
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Active Loans</h3>
            <p class="text-sm text-muted-foreground">Your currently active loans</p>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($activeLoans as $loan): ?>
            <div class="border border-border rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold"><?= formatCurrency($loan['amount']) ?> Loan</h4>
                    <span class="px-2 py-1 text-xs rounded border border-green-200 text-green-600 bg-green-50">
                        Active
                    </span>
                </div>
                
                <div class="grid md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <span class="text-sm text-muted-foreground">Monthly Payment</span>
                        <div class="font-medium"><?= formatCurrency($loan['monthlyPayment']) ?></div>
                    </div>
                    <div>
                        <span class="text-sm text-muted-foreground">Progress</span>
                        <div class="font-medium"><?= $loan['paymentsMade'] ?>/<?= $loan['totalPayments'] ?></div>
                    </div>
                    <div>
                        <span class="text-sm text-muted-foreground">Next Payment</span>
                        <div class="font-medium"><?= isset($loan['nextPaymentDate']) ? formatDate($loan['nextPaymentDate']) : 'N/A' ?></div>
                    </div>
                    <div>
                        <span class="text-sm text-muted-foreground">Start Date</span>
                        <div class="font-medium"><?= isset($loan['startDate']) ? formatDate($loan['startDate']) : 'N/A' ?></div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-muted-foreground mb-1">
                        <span>Payment Progress</span>
                        <span><?= round(($loan['paymentsMade'] / $loan['totalPayments']) * 100) ?>%</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div 
                            class="bg-primary h-2 rounded-full transition-all duration-300" 
                            style="width: <?= ($loan['paymentsMade'] / $loan['totalPayments']) * 100 ?>%"
                        ></div>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button class="text-primary hover:text-primary/80 text-sm">
                        <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
                        View Agreement
                    </button>
                    <button class="text-primary hover:text-primary/80 text-sm">
                        <i data-lucide="download" class="w-4 h-4 inline mr-1"></i>
                        Payment Schedule
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Loan History -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Loan History</h3>
            <p class="text-sm text-muted-foreground">Complete history of all your loans</p>
        </div>
        <div class="overflow-x-auto">
            <?php if (count($loans) > 0): ?>
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Term
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
                    <?php 
                    // Sort loans by application date (newest first)
                    usort($loans, fn($a, $b) => strtotime($b['applicationDate']) - strtotime($a['applicationDate']));
                    foreach ($loans as $loan): 
                    ?>
                    <tr class="hover:bg-muted/50">
                        <td class="px-6 py-4 text-sm font-medium text-foreground">
                            <?= formatCurrency($loan['amount']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
                            <?= $loan['termMonths'] ?> months
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded border <?= getLoanStatusClass($loan['status']) ?>">
                                <?= ucfirst($loan['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            <?= formatDate($loan['applicationDate']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <button class="text-primary hover:text-primary/80" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <?php if ($loan['status'] === 'approved' && !$loan['agreementSigned']): ?>
                                <button class="text-green-600 hover:text-green-800" title="Sign Agreement">
                                    <i data-lucide="file-signature" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-6 text-center">
                <i data-lucide="credit-card" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                <h3 class="text-lg font-medium mb-2">No loans yet</h3>
                <p class="text-muted-foreground">Apply for your first loan using the form above.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Loan calculation
function calculateLoan() {
    const amount = parseFloat(document.getElementById('loanAmount').value) || 0;
    const termMonths = parseInt(document.getElementById('termMonths').value) || 0;
    
    if (amount > 0 && termMonths > 0) {
        const interestRate = <?= $_SESSION['app_data']['interestRate'] ?> / 100;
        const totalPayments = termMonths * 2; // Bi-monthly payments
        const monthlyInterest = interestRate / 24; // Bi-monthly rate
        
        // Calculate bi-monthly payment using loan formula
        const payment = amount * (monthlyInterest * Math.pow(1 + monthlyInterest, totalPayments)) / (Math.pow(1 + monthlyInterest, totalPayments) - 1);
        const totalAmount = payment * totalPayments;
        const totalInterest = totalAmount - amount;
        
        document.getElementById('monthlyPayment').textContent = formatCurrency(payment);
        document.getElementById('totalPayments').textContent = totalPayments;
        document.getElementById('totalInterest').textContent = formatCurrency(totalInterest);
        document.getElementById('loanPreview').classList.remove('hidden');
    } else {
        document.getElementById('loanPreview').classList.add('hidden');
    }
}

// Add event listeners for loan calculation
document.getElementById('loanAmount').addEventListener('input', calculateLoan);
document.getElementById('termMonths').addEventListener('change', calculateLoan);

// Loan application form
document.getElementById('loanApplicationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('loanAmount').value);
    const termMonths = parseInt(document.getElementById('termMonths').value);
    const purpose = document.getElementById('loanPurpose').value;
    
    if (!amount || amount < 1000 || amount > 50000) {
        showToast('Please enter a valid loan amount between $1,000 and $50,000', 'error');
        return;
    }
    
    if (!termMonths) {
        showToast('Please select a loan term', 'error');
        return;
    }
    
    if (!purpose.trim()) {
        showToast('Please provide the purpose of the loan', 'error');
        return;
    }
    
    // Here you would submit the loan application
    showToast('Loan application submitted successfully! It will be reviewed by admin.', 'success');
    
    // Reset form
    this.reset();
    document.getElementById('loanPreview').classList.add('hidden');
});

// Format currency helper
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
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