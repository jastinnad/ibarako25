<?php
// This file is included in member/dashboard.php
if (!isset($user)) {
    exit('Access denied');
}

// Get member contributions
$contributions = getContributionsByMember($user['member_id']);

// Handle add contribution
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add_contribution') {
    $amount = floatval($_POST['amount'] ?? 0);
    $type = $_POST['type'] ?? 'monthly';
    $description = trim($_POST['description'] ?? '');
    
    if ($amount <= 0) {
        showAlert('Please enter a valid amount', 'error');
    } else {
        $contributionData = [
            'member_id' => $user['member_id'],
            'amount' => $amount,
            'date' => date('Y-m-d'),
            'type' => $type,
            'description' => $description ?: ($type === 'monthly' ? 'Monthly contribution' : 'Additional savings')
        ];
        
        if (createContribution($contributionData)) {
            showAlert('Contribution added successfully', 'success');
            // Refresh contributions
            $contributions = getContributionsByMember($user['member_id']);
        } else {
            showAlert('Failed to add contribution', 'error');
        }
    }
}

$totalContributions = array_sum(array_column($contributions, 'amount'));
$monthlyContributions = array_filter($contributions, fn($c) => $c['type'] === 'monthly');
$additionalContributions = array_filter($contributions, fn($c) => $c['type'] === 'additional');
$monthlyTotal = array_sum(array_column($monthlyContributions, 'amount'));
$additionalTotal = array_sum(array_column($additionalContributions, 'amount'));
?>

<div class="container mx-auto" style="max-width: 1400px;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Contributions</h2>
            <p class="text-muted-foreground">Manage your savings and contributions</p>
        </div>
        <button onclick="openModal('addContributionModal')" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>
            Add Contribution
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Total Contributions</h3>
                <i class="fas fa-dollar-sign text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo formatCurrency($totalContributions); ?></div>
                <p class="text-sm text-muted-foreground">
                    From <?php echo count($contributions); ?> contributions
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Monthly Contributions</h3>
                <i class="fas fa-calendar text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo formatCurrency($monthlyTotal); ?></div>
                <p class="text-sm text-muted-foreground">
                    <?php echo count($monthlyContributions); ?> monthly contributions
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Additional Savings</h3>
                <i class="fas fa-chart-line text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo formatCurrency($additionalTotal); ?></div>
                <p class="text-sm text-muted-foreground">
                    <?php echo count($additionalContributions); ?> additional contributions
                </p>
            </div>
        </div>
    </div>

    <!-- Contributions Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Contribution History</h3>
            <p class="card-description">
                All your contributions and savings history
            </p>
        </div>
        <div class="card-content">
            <?php if (empty($contributions)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-dollar-sign text-muted-foreground mb-4" style="font-size: 3rem;"></i>
                    <h3 class="text-lg font-medium mb-2">No contributions yet</h3>
                    <p class="text-muted-foreground mb-4">
                        Start building your savings by adding your first contribution
                    </p>
                    <button onclick="openModal('addContributionModal')" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Contribution
                    </button>
                </div>
            <?php else: ?>
                <div class="overflow-auto">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Sort contributions by date (newest first)
                            usort($contributions, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                            
                            foreach ($contributions as $contribution): ?>
                                <tr>
                                    <td><?php echo formatDate($contribution['date']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $contribution['type'] === 'monthly' ? 'badge-default' : 'badge-secondary'; ?>">
                                            <?php echo ucfirst($contribution['type']); ?>
                                        </span>
                                    </td>
                                    <td class="font-medium"><?php echo htmlspecialchars($contribution['description']); ?></td>
                                    <td class="text-right font-medium">
                                        <?php echo formatCurrency($contribution['amount']); ?>
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

<!-- Add Contribution Modal -->
<div class="modal" id="addContributionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Contribution</h3>
            <p class="modal-description">
                Add a new contribution to your account
            </p>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_contribution">
            
            <div class="form-group">
                <label class="label" for="amount">Amount</label>
                <input class="input" id="amount" name="amount" type="number" step="0.01" min="0.01" placeholder="Enter amount" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="type">Type</label>
                <select class="select" id="type" name="type" required>
                    <option value="monthly">Monthly Contribution</option>
                    <option value="additional">Additional Savings</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="label" for="description">Description (Optional)</label>
                <input class="input" id="description" name="description" placeholder="Enter description">
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addContributionModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Contribution</button>
            </div>
        </form>
    </div>
</div>