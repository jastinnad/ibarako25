<?php
// This file is included in admin/dashboard.php
if (!isAdmin()) {
    exit('Access denied');
}

// Handle member actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_member') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        if (empty($name) || empty($email) || empty($mobile) || empty($address)) {
            showAlert('Please fill in all fields', 'error');
        } elseif (getUserByEmail($email)) {
            showAlert('Email already exists', 'error');
        } else {
            $userData = [
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'address' => $address,
                'role' => 'member',
                'status' => $status,
                'password' => 'password' // Default password
            ];
            
            if (createUser($userData)) {
                $memberId = generateMemberId();
                showAlert("Member {$memberId} added successfully", 'success');
            } else {
                showAlert('Failed to add member', 'error');
            }
        }
    } elseif ($action === 'update_member') {
        $id = intval($_POST['member_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        if ($id > 0 && !empty($name) && !empty($email)) {
            $userData = [
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'address' => $address,
                'status' => $status
            ];
            
            if (updateUser($id, $userData)) {
                showAlert('Member updated successfully', 'success');
            } else {
                showAlert('Failed to update member', 'error');
            }
        }
    } elseif ($action === 'delete_member') {
        $id = intval($_POST['member_id'] ?? 0);
        if ($id > 0) {
            if (deleteUser($id)) {
                showAlert('Member deleted successfully', 'success');
            } else {
                showAlert('Failed to delete member', 'error');
            }
        }
    }
}

$members = getAllMembers();
$activeMembers = array_filter($members, fn($m) => $m['status'] === 'active');
$inactiveMembers = array_filter($members, fn($m) => $m['status'] === 'inactive');
?>

<div class="container mx-auto" style="max-width: 1600px;">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Member Management</h2>
            <p class="text-muted-foreground">Manage member accounts and information</p>
        </div>
        <button onclick="openModal('addMemberModal')" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>
            Add Member
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Total Members</h3>
                <i class="fas fa-users text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($members); ?></div>
                <p class="text-sm text-muted-foreground">Registered members</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Active Members</h3>
                <i class="fas fa-user-plus text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($activeMembers); ?></div>
                <p class="text-sm text-muted-foreground">Active accounts</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between" style="padding-bottom: 0.5rem;">
                <h3 class="text-sm font-medium">Inactive Members</h3>
                <i class="fas fa-user-times text-muted-foreground"></i>
            </div>
            <div class="card-content" style="padding-top: 0;">
                <div class="text-2xl font-bold"><?php echo count($inactiveMembers); ?></div>
                <p class="text-sm text-muted-foreground">Inactive accounts</p>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Members List</h3>
            <p class="card-description">All registered members and their account information</p>
        </div>
        <div class="card-content">
            <div class="overflow-auto">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Loans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): 
                            $memberLoans = getLoansByMember($member['member_id']);
                            $memberContributions = getContributionsByMember($member['member_id']);
                        ?>
                            <tr>
                                <td class="font-mono"><?php echo htmlspecialchars($member['member_id']); ?></td>
                                <td class="font-medium"><?php echo htmlspecialchars($member['name']); ?></td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td><?php echo htmlspecialchars($member['mobile']); ?></td>
                                <td><?php echo formatDate($member['join_date']); ?></td>
                                <td>
                                    <span class="badge <?php echo $member['status'] === 'active' ? 'badge-default' : 'badge-secondary'; ?>">
                                        <?php echo ucfirst($member['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo count($memberLoans); ?> loans</div>
                                        <div class="text-muted-foreground">
                                            <?php echo count($memberContributions); ?> contributions
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button onclick="editMember(<?php echo htmlspecialchars(json_encode($member)); ?>)" class="btn btn-outline btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteMember(<?php echo $member['id']; ?>, '<?php echo htmlspecialchars($member['member_id']); ?>')" class="btn btn-outline btn-sm">
                                            <i class="fas fa-trash"></i>
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

<!-- Add Member Modal -->
<div class="modal" id="addMemberModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Member</h3>
            <p class="modal-description">Create a new member account with a unique member ID</p>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_member">
            
            <div class="form-group">
                <label class="label" for="name">Full Name</label>
                <input class="input" id="name" name="name" placeholder="Enter full name" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="email">Email Address</label>
                <input class="input" id="email" name="email" type="email" placeholder="Enter email address" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="mobile">Mobile Number</label>
                <input class="input" id="mobile" name="mobile" placeholder="Enter mobile number" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="address">Address</label>
                <input class="input" id="address" name="address" placeholder="Enter address" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="status">Status</label>
                <select class="select" id="status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="p-3 bg-accent rounded">
                <p class="text-sm" style="color: #1e40af;">
                    <strong>Member ID:</strong> Will be auto-generated (MBR-XXXX format)
                </p>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addMemberModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Member</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal" id="editMemberModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Member</h3>
            <p class="modal-description">Update member information</p>
        </div>
        <form method="POST" id="editMemberForm">
            <input type="hidden" name="action" value="update_member">
            <input type="hidden" name="member_id" id="edit_member_id">
            
            <div class="form-group">
                <label class="label" for="edit_name">Full Name</label>
                <input class="input" id="edit_name" name="name" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="edit_email">Email Address</label>
                <input class="input" id="edit_email" name="email" type="email" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="edit_mobile">Mobile Number</label>
                <input class="input" id="edit_mobile" name="mobile" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="edit_address">Address</label>
                <input class="input" id="edit_address" name="address" required>
            </div>
            
            <div class="form-group">
                <label class="label" for="edit_status">Status</label>
                <select class="select" id="edit_status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editMemberModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Member</button>
            </div>
        </form>
    </div>
</div>

<script>
function editMember(member) {
    document.getElementById('edit_member_id').value = member.id;
    document.getElementById('edit_name').value = member.name;
    document.getElementById('edit_email').value = member.email;
    document.getElementById('edit_mobile').value = member.mobile;
    document.getElementById('edit_address').value = member.address;
    document.getElementById('edit_status').value = member.status;
    openModal('editMemberModal');
}

function deleteMember(id, memberId) {
    if (confirm(`Are you sure you want to delete member ${memberId}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_member">
            <input type="hidden" name="member_id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>