<?php
$users = $_SESSION['app_data']['users'];
$members = array_filter($users, fn($u) => $u['role'] === 'member');
?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-semibold">Member Management</h2>
        <p class="text-muted-foreground">Manage all system members</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid md:grid-cols-4 gap-6">
        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Total Members</h3>
                <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count($members) ?></div>
            <p class="text-xs text-muted-foreground">All registered members</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Active Members</h3>
                <i data-lucide="check-circle" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count(array_filter($members, fn($m) => $m['status'] === 'active')) ?></div>
            <p class="text-xs text-muted-foreground">Currently active</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">Pending Members</h3>
                <i data-lucide="clock" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold"><?= count(array_filter($members, fn($m) => $m['status'] === 'pending')) ?></div>
            <p class="text-xs text-muted-foreground">Awaiting approval</p>
        </div>

        <div class="bg-white border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-muted-foreground">This Month</h3>
                <i data-lucide="calendar" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="text-2xl font-bold">
                <?= count(array_filter($members, fn($m) => date('Y-m', strtotime($m['joinDate'])) === date('Y-m'))) ?>
            </div>
            <p class="text-xs text-muted-foreground">New this month</p>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">All Members</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Member
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Join Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($members as $member): ?>
                    <tr class="hover:bg-muted/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary">
                                            <?= strtoupper(substr($member['name'], 0, 2)) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-foreground">
                                        <?= htmlspecialchars($member['name']) ?>
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        <?= htmlspecialchars($member['memberId']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-foreground"><?= htmlspecialchars($member['email']) ?></div>
                            <div class="text-sm text-muted-foreground"><?= htmlspecialchars($member['mobile']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground">
                            <?= formatDate($member['joinDate']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded border <?= getMemberStatusClass($member['status']) ?>">
                                <?= ucfirst($member['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <button class="text-primary hover:text-primary/80">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button class="text-muted-foreground hover:text-foreground">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <?php if ($member['status'] === 'pending'): ?>
                                <button 
                                    onclick="changeUserStatus('<?= $member['id'] ?>', 'active')"
                                    class="text-green-600 hover:text-green-800"
                                    title="Approve"
                                >
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                                <button 
                                    onclick="changeUserStatus('<?= $member['id'] ?>', 'inactive')"
                                    class="text-red-600 hover:text-red-800"
                                    title="Reject"
                                >
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
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
function changeUserStatus(userId, status) {
    // This would be implemented to change user status
    // For now, just show a placeholder message
    showToast(`User status would be changed to ${status}`, 'info');
}
</script>

<?php
function getMemberStatusClass($status) {
    switch ($status) {
        case 'active':
            return 'border-green-200 text-green-600 bg-green-50';
        case 'pending':
            return 'border-orange-200 text-orange-600 bg-orange-50';
        case 'inactive':
            return 'border-red-200 text-red-600 bg-red-50';
        default:
            return 'border-gray-200 text-gray-600 bg-gray-50';
    }
}
?>