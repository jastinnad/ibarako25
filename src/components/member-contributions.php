<?php
$contributions = array_filter($_SESSION['app_data']['contributions'], fn($c) => $c['memberId'] === $currentUser['memberId']);
$totalContributions = array_sum(array_column($contributions, 'amount'));
$monthlyContributions = array_filter($contributions, fn($c) => $c['type'] === 'monthly');
$additionalContributions = array_filter($contributions, fn($c) => $c['type'] === 'additional');
?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-semibold">My Contributions</h2>
        <p class="text-muted-foreground">Track and manage your savings contributions</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid md:grid-cols-3 gap-6">
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
                <h3 class="text-sm font-medium text-muted-foreground">Monthly Contributions</h3>
                <i data-lucide="calendar" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($monthlyContributions) ?></div>
            <p class="text-xs text-muted-foreground">Regular payments</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Additional Savings</h3>
                <i data-lucide="plus-circle" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($additionalContributions) ?></div>
            <p class="text-xs text-muted-foreground">Extra contributions</p>
        </div>
    </div>

    <!-- Add New Contribution -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Add New Contribution</h3>
            <p class="text-sm text-muted-foreground">Record a new savings contribution</p>
        </div>
        <div class="p-6">
            <form id="contributionForm" class="space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label for="amount" class="text-sm font-medium">Amount</label>
                        <input 
                            id="amount" 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            placeholder="0.00" 
                            class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                            required
                        >
                    </div>

                    <div class="space-y-2">
                        <label for="type" class="text-sm font-medium">Type</label>
                        <select 
                            id="type" 
                            class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                            required
                        >
                            <option value="">Select type</option>
                            <option value="monthly">Monthly Contribution</option>
                            <option value="additional">Additional Savings</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="date" class="text-sm font-medium">Date</label>
                        <input 
                            id="date" 
                            type="date" 
                            value="<?= date('Y-m-d') ?>"
                            class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                            required
                        >
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="description" class="text-sm font-medium">Description</label>
                    <input 
                        id="description" 
                        placeholder="Enter description (optional)" 
                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                </div>

                <button 
                    type="submit" 
                    class="bg-primary text-primary-foreground px-6 py-2 rounded-md hover:bg-primary/90 transition-colors"
                >
                    Add Contribution
                </button>
            </form>
        </div>
    </div>

    <!-- Contributions History -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Contribution History</h3>
            <p class="text-sm text-muted-foreground">Your complete contribution record</p>
        </div>
        <div class="overflow-x-auto">
            <?php if (count($contributions) > 0): ?>
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Amount
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php 
                    // Sort contributions by date (newest first)
                    usort($contributions, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                    foreach ($contributions as $contribution): 
                    ?>
                    <tr class="hover:bg-muted/50">
                        <td class="px-6 py-4 text-sm text-foreground">
                            <?= formatDate($contribution['date']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-foreground">
                                <?= htmlspecialchars($contribution['description']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded border <?= getContributionTypeClass($contribution['type']) ?>">
                                <?= ucfirst(str_replace('_', ' ', $contribution['type'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-green-600">
                            +<?= formatCurrency($contribution['amount']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-6 text-center">
                <i data-lucide="piggy-bank" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                <h3 class="text-lg font-medium mb-2">No contributions yet</h3>
                <p class="text-muted-foreground">Start by adding your first contribution above.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('contributionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('amount').value);
    const type = document.getElementById('type').value;
    const date = document.getElementById('date').value;
    const description = document.getElementById('description').value;
    
    if (!amount || amount <= 0) {
        showToast('Please enter a valid amount', 'error');
        return;
    }
    
    if (!type) {
        showToast('Please select a contribution type', 'error');
        return;
    }
    
    if (!date) {
        showToast('Please select a date', 'error');
        return;
    }
    
    // Here you would submit the contribution
    showToast('Contribution added successfully!', 'success');
    
    // Reset form
    this.reset();
    document.getElementById('date').value = new Date().toISOString().split('T')[0];
});
</script>

<?php
function getContributionTypeClass($type) {
    switch ($type) {
        case 'monthly':
            return 'border-blue-200 text-blue-600 bg-blue-50';
        case 'additional':
            return 'border-purple-200 text-purple-600 bg-purple-50';
        default:
            return 'border-gray-200 text-gray-600 bg-gray-50';
    }
}
?>