<?php
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$error = '';

if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'member';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $user = getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password']) && $user['role'] === $role) {
            $_SESSION['user_id'] = $user['id'];
            redirect('/');
        } else {
            $error = 'Invalid credentials or role mismatch';
        }
    }
}

$pageTitle = 'Login - Loan Management System';
include 'includes/header.php';
?>

<div class="min-h-screen landing-gradient">
    <!-- Header -->
    <header class="py-6" style="background: rgba(255, 255, 255, 0.1);">
        <div class="container">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div style="width: 2rem; height: 2rem; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-piggy-bank" style="color: var(--primary);"></i>
                    </div>
                    <h1 class="text-xl font-semibold text-white">LoanSystem</h1>
                </div>
                <div class="text-sm" style="color: rgba(255, 255, 255, 0.8);">
                    Secure Loan Management System
                </div>
            </div>
        </div>
    </header>

    <div class="container py-12">
        <div class="grid gap-12 items-center" style="grid-template-columns: 1fr 400px;">
            <!-- Left side - Features -->
            <div class="text-white">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-4">
                        Manage Your Loans with Confidence
                    </h2>
                    <p class="text-lg mb-8" style="color: rgba(255, 255, 255, 0.9);">
                        A comprehensive loan management system designed for members and administrators.
                    </p>
                </div>

                <div class="grid gap-6">
                    <div class="flex items-start gap-4">
                        <div style="width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Member Portal</h3>
                            <p style="color: rgba(255, 255, 255, 0.8);">
                                Apply for loans, track contributions, and manage your profile with ease.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div style="width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Admin Management</h3>
                            <p style="color: rgba(255, 255, 255, 0.8);">
                                Complete control over member accounts, loan approvals, and system settings.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div style="width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Flexible Payments</h3>
                            <p style="color: rgba(255, 255, 255, 0.8);">
                                Bi-monthly payment schedule with transparent interest calculations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side - Login Form -->
            <div class="w-full">
                <div class="tabs">
                    <div class="tabs-list">
                        <button class="tabs-trigger active" onclick="openTab(event, 'loginTab')">Login</button>
                        <button class="tabs-trigger" onclick="openTab(event, 'signupTab')">Sign Up</button>
                    </div>

                    <!-- Login Tab -->
                    <div id="loginTab" class="tabs-content active">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Welcome Back</h3>
                                <p class="card-description">Sign in to your account to continue</p>
                            </div>
                            <div class="card-content">
                                <?php if ($error): ?>
                                    <div class="alert alert-error mb-4">
                                        <span><?php echo htmlspecialchars($error); ?></span>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" id="loginForm">
                                    <div class="form-group">
                                        <label class="label" for="email">Email Address</label>
                                        <input class="input" id="email" name="email" type="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="label" for="password">Password</label>
                                        <input class="input" id="password" name="password" type="password" placeholder="Enter your password" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="label">Login as</label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2">
                                                <input type="radio" name="role" value="member" <?php echo ($_POST['role'] ?? 'member') === 'member' ? 'checked' : ''; ?>>
                                                <span>Member</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <input type="radio" name="role" value="admin" <?php echo ($_POST['role'] ?? '') === 'admin' ? 'checked' : ''; ?>>
                                                <span>Admin</span>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-full">Sign In</button>
                                </form>

                                <div class="mt-4 p-3 bg-muted rounded">
                                    <p class="text-sm text-muted-foreground mb-2">Demo Credentials:</p>
                                    <p class="text-sm">
                                        <strong>Admin:</strong> admin@loan.com / password<br>
                                        <strong>Member:</strong> john@email.com / password
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signup Tab -->
                    <div id="signupTab" class="tabs-content">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Create Account</h3>
                                <p class="card-description">Register as a new member</p>
                            </div>
                            <div class="card-content">
                                <form method="POST" action="register.php" id="signupForm">
                                    <div class="form-group">
                                        <label class="label" for="signup-name">Full Name</label>
                                        <input class="input" id="signup-name" name="name" placeholder="Enter your full name" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="label" for="signup-email">Email Address</label>
                                        <input class="input" id="signup-email" name="email" type="email" placeholder="Enter your email" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="label" for="signup-mobile">Mobile Number</label>
                                        <input class="input" id="signup-mobile" name="mobile" placeholder="Enter your mobile number" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="label" for="signup-address">Address</label>
                                        <input class="input" id="signup-address" name="address" placeholder="Enter your address" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="label" for="signup-password">Password</label>
                                        <input class="input" id="signup-password" name="password" type="password" placeholder="Create a password" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="label" for="signup-confirm">Confirm Password</label>
                                        <input class="input" id="signup-confirm" name="confirm_password" type="password" placeholder="Confirm your password" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-full">Create Account</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
@media (max-width: 1024px) {
    .container .grid {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
}
</style>