<?php
// This file is included in member/dashboard.php
if (!isset($user)) {
    exit('Access denied');
}

// Handle profile update request
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_request') {
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (!empty($mobile) && !empty($email)) {
        $notificationData = [
            'type' => 'profile_update',
            'member_id' => $user['member_id'],
            'member_name' => $user['name'],
            'message' => "Profile update request - Mobile: {$mobile}, Email: {$email}",
            'data' => ['mobile' => $mobile, 'email' => $email]
        ];
        
        if (createNotification($notificationData)) {
            showAlert('Update request submitted successfully', 'success');
        } else {
            showAlert('Failed to submit request', 'error');
        }
    } else {
        showAlert('Please fill in all fields', 'error');
    }
}
?>

<div class="container mx-auto" style="max-width: 1200px;">
    <!-- Profile Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Profile Information</h2>
            <p class="text-muted-foreground">View your personal details</p>
        </div>
        <button onclick="openModal('updateModal')" class="btn btn-outline">
            <i class="fas fa-edit mr-2"></i>
            Request Update
        </button>
    </div>

    <!-- Profile Details Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title flex items-center gap-2">
                <i class="fas fa-credit-card"></i>
                <span>Member Information</span>
            </h3>
            <p class="card-description">
                Your profile information is managed by the system administrator
            </p>
        </div>
        <div class="card-content">
            <div class="grid grid-cols-1 gap-6" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <div class="grid gap-4">
                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Member ID</label>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="badge badge-secondary font-mono">
                                <?php echo htmlspecialchars($user['member_id']); ?>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Full Name</label>
                        <input class="input mt-1" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
                    </div>

                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Email Address</label>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="fas fa-envelope text-muted-foreground"></i>
                            <input class="input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>
                    </div>

                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Mobile Number</label>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="fas fa-phone text-muted-foreground"></i>
                            <input class="input" value="<?php echo htmlspecialchars($user['mobile']); ?>" disabled>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Address</label>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="fas fa-map-marker-alt text-muted-foreground"></i>
                            <input class="input" value="<?php echo htmlspecialchars($user['address']); ?>" disabled>
                        </div>
                    </div>

                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Join Date</label>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="fas fa-calendar text-muted-foreground"></i>
                            <input class="input" value="<?php echo formatDate($user['join_date']); ?>" disabled>
                        </div>
                    </div>

                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Account Status</label>
                        <div class="mt-1">
                            <span class="badge <?php echo $user['status'] === 'active' ? 'badge-default' : 'badge-secondary'; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="label text-sm font-medium text-muted-foreground">Role</label>
                        <div class="mt-1">
                            <span class="badge badge-outline">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Notice -->
    <div class="card mt-6" style="border-color: #3b82f6; background-color: #eff6ff;">
        <div class="card-content">
            <div class="flex items-start gap-3">
                <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; margin-top: 8px; flex-shrink: 0;"></div>
                <div>
                    <p class="text-sm" style="color: #1e40af;">
                        <strong>Update Requests:</strong> You can request updates to your mobile number and email address using the "Request Update" button. 
                        All other profile changes must be made by the system administrator.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Request Modal -->
<div class="modal" id="updateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Request Profile Update</h3>
            <p class="modal-description">
                You can only request updates to your mobile number and email address. Other changes require admin approval.
            </p>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update_request">
            
            <div class="form-group">
                <label class="label" for="update-mobile">Mobile Number</label>
                <input class="input" id="update-mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="update-email">Email Address</label>
                <input class="input" id="update-email" name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal('updateModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>