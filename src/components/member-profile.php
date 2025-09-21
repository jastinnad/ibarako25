<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-semibold">My Profile</h2>
        <p class="text-muted-foreground">View and manage your profile information</p>
    </div>

    <!-- Profile Information -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Personal Information</h3>
            <p class="text-sm text-muted-foreground">Your basic account information</p>
        </div>
        <div class="p-6 space-y-6">
            <!-- Profile Picture -->
            <div class="flex items-center space-x-4">
                <div class="h-20 w-20 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-2xl font-medium text-primary">
                        <?= strtoupper(substr($currentUser['name'], 0, 2)) ?>
                    </span>
                </div>
                <div>
                    <h4 class="font-semibold"><?= htmlspecialchars($currentUser['name']) ?></h4>
                    <p class="text-sm text-muted-foreground"><?= htmlspecialchars($currentUser['memberId']) ?></p>
                    <p class="text-xs text-muted-foreground">Member since <?= formatDate($currentUser['joinDate']) ?></p>
                </div>
            </div>

            <!-- Information Fields -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Full Name</label>
                    <div class="px-3 py-2 border border-border rounded-md bg-muted/50">
                        <?= htmlspecialchars($currentUser['name']) ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Member ID</label>
                    <div class="px-3 py-2 border border-border rounded-md bg-muted/50">
                        <?= htmlspecialchars($currentUser['memberId']) ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Email Address</label>
                    <div class="px-3 py-2 border border-border rounded-md bg-muted/50">
                        <?= htmlspecialchars($currentUser['email']) ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Mobile Number</label>
                    <div class="px-3 py-2 border border-border rounded-md bg-muted/50">
                        <?= htmlspecialchars($currentUser['mobile']) ?>
                    </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-medium text-muted-foreground">Address</label>
                    <div class="px-3 py-2 border border-border rounded-md bg-muted/50">
                        <?= htmlspecialchars($currentUser['address']) ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Join Date</label>
                    <div class="px-3 py-2 border border-border rounded-md bg-muted/50">
                        <?= formatDate($currentUser['joinDate']) ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Account Status</label>
                    <div class="px-3 py-2 border border-border rounded-md bg-muted/50">
                        <span class="px-2 py-1 text-xs rounded border <?= getMemberStatusClass($currentUser['status']) ?>">
                            <?= ucfirst($currentUser['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Request Section -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Request Profile Update</h3>
            <p class="text-sm text-muted-foreground">Submit a request to update your mobile number or email address</p>
        </div>
        <div class="p-6">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <i data-lucide="info" class="w-5 h-5 text-orange-600 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-medium text-orange-800">Profile Update Policy</h4>
                        <p class="text-sm text-orange-700 mt-1">
                            For security reasons, updates to your contact information require admin approval. 
                            Only mobile number and email address can be updated.
                        </p>
                    </div>
                </div>
            </div>

            <form id="updateRequestForm" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="newMobile" class="text-sm font-medium">New Mobile Number</label>
                        <input 
                            id="newMobile" 
                            placeholder="Enter new mobile number" 
                            class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                    </div>

                    <div class="space-y-2">
                        <label for="newEmail" class="text-sm font-medium">New Email Address</label>
                        <input 
                            id="newEmail" 
                            type="email" 
                            placeholder="Enter new email address" 
                            class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="updateReason" class="text-sm font-medium">Reason for Update</label>
                    <textarea 
                        id="updateReason" 
                        rows="3" 
                        placeholder="Please explain why you need to update your information" 
                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    ></textarea>
                </div>

                <button 
                    type="submit" 
                    class="bg-primary text-primary-foreground px-6 py-2 rounded-md hover:bg-primary/90 transition-colors"
                >
                    Submit Update Request
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Update Requests -->
    <div class="bg-white border border-border rounded-lg">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold">Recent Update Requests</h3>
            <p class="text-sm text-muted-foreground">History of your profile update requests</p>
        </div>
        <div class="p-6">
            <div class="text-center py-8">
                <i data-lucide="file-text" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                <h3 class="text-lg font-medium mb-2">No update requests</h3>
                <p class="text-muted-foreground">You haven't submitted any profile update requests yet.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('updateRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newMobile = document.getElementById('newMobile').value;
    const newEmail = document.getElementById('newEmail').value;
    const reason = document.getElementById('updateReason').value;
    
    if (!newMobile && !newEmail) {
        showToast('Please provide at least one field to update', 'error');
        return;
    }
    
    if (!reason.trim()) {
        showToast('Please provide a reason for the update', 'error');
        return;
    }
    
    // Here you would submit the update request
    showToast('Profile update request submitted successfully!', 'success');
    
    // Reset form
    this.reset();
});
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