<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="piggy-bank" class="h-8 w-8 text-primary"></i>
                    <h1 class="text-xl font-semibold">LoanSystem</h1>
                </div>
                <div class="text-sm text-muted-foreground">
                    Secure Loan Management System
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left side - Features -->
            <div class="space-y-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Manage Your Loans with Confidence
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        A comprehensive loan management system designed for members and administrators.
                    </p>
                </div>

                <div class="grid gap-6">
                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i data-lucide="users" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Member Portal</h3>
                            <p class="text-gray-600">
                                Apply for loans, track contributions, and manage your profile with ease.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i data-lucide="shield" class="h-6 w-6 text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Admin Management</h3>
                            <p class="text-gray-600">
                                Complete control over member accounts, loan approvals, and system settings.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i data-lucide="credit-card" class="h-6 w-6 text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Flexible Payments</h3>
                            <p class="text-gray-600">
                                Bi-monthly payment schedule with transparent interest calculations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side - Login/Signup -->
            <div class="max-w-md mx-auto w-full">
                <div class="w-full">
                    <!-- Tab Navigation -->
                    <div class="grid grid-cols-2 bg-muted p-1 rounded-lg mb-6">
                        <button id="loginTab" class="px-3 py-2 text-sm font-medium rounded-md bg-background text-foreground shadow-sm">
                            Login
                        </button>
                        <button id="signupTab" class="px-3 py-2 text-sm font-medium rounded-md text-muted-foreground">
                            Sign Up
                        </button>
                    </div>

                    <!-- Login Form -->
                    <div id="loginContent" class="bg-white border border-border rounded-lg shadow-sm">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-lg font-semibold">Welcome Back</h3>
                            <p class="text-sm text-muted-foreground">
                                Sign in to your account to continue
                            </p>
                        </div>
                        <div class="p-6">
                            <form id="loginForm" class="space-y-4">
                                <div class="space-y-2">
                                    <label for="loginEmail" class="text-sm font-medium">Email Address</label>
                                    <input 
                                        id="loginEmail" 
                                        type="email" 
                                        placeholder="Enter your email" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label for="loginPassword" class="text-sm font-medium">Password</label>
                                    <input 
                                        id="loginPassword" 
                                        type="password" 
                                        placeholder="Enter your password" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Login as</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="role" value="member" checked class="text-primary">
                                            <span class="text-sm">Member</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="role" value="admin" class="text-primary">
                                            <span class="text-sm">Admin</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90 transition-colors">
                                    Sign In
                                </button>
                            </form>

                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 mb-2">Demo Credentials:</p>
                                <p class="text-xs text-gray-500">
                                    <strong>Admin:</strong> admin@loan.com / password<br>
                                    <strong>Member:</strong> john@email.com / password
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Signup Form -->
                    <div id="signupContent" class="bg-white border border-border rounded-lg shadow-sm hidden">
                        <div class="p-6 border-b border-border">
                            <h3 class="text-lg font-semibold">Create Account</h3>
                            <p class="text-sm text-muted-foreground">
                                Register as a new member
                            </p>
                        </div>
                        <div class="p-6">
                            <form id="signupForm" class="space-y-4">
                                <div class="space-y-2">
                                    <label for="signupName" class="text-sm font-medium">Full Name</label>
                                    <input 
                                        id="signupName" 
                                        placeholder="Enter your full name" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label for="signupEmail" class="text-sm font-medium">Email Address</label>
                                    <input 
                                        id="signupEmail" 
                                        type="email" 
                                        placeholder="Enter your email" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label for="signupMobile" class="text-sm font-medium">Mobile Number</label>
                                    <input 
                                        id="signupMobile" 
                                        placeholder="Enter your mobile number" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label for="signupAddress" class="text-sm font-medium">Address</label>
                                    <input 
                                        id="signupAddress" 
                                        placeholder="Enter your address" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label for="signupPassword" class="text-sm font-medium">Password</label>
                                    <input 
                                        id="signupPassword" 
                                        type="password" 
                                        placeholder="Create a password" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <div class="space-y-2">
                                    <label for="signupConfirm" class="text-sm font-medium">Confirm Password</label>
                                    <input 
                                        id="signupConfirm" 
                                        type="password" 
                                        placeholder="Confirm your password" 
                                        class="w-full px-3 py-2 border border-border rounded-md bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                                        required
                                    >
                                </div>

                                <button type="submit" class="w-full bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90 transition-colors">
                                    Create Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginContent = document.getElementById('loginContent');
    const signupContent = document.getElementById('signupContent');

    loginTab.addEventListener('click', () => {
        loginTab.classList.add('bg-background', 'text-foreground', 'shadow-sm');
        loginTab.classList.remove('text-muted-foreground');
        signupTab.classList.remove('bg-background', 'text-foreground', 'shadow-sm');
        signupTab.classList.add('text-muted-foreground');
        
        loginContent.classList.remove('hidden');
        signupContent.classList.add('hidden');
    });

    signupTab.addEventListener('click', () => {
        signupTab.classList.add('bg-background', 'text-foreground', 'shadow-sm');
        signupTab.classList.remove('text-muted-foreground');
        loginTab.classList.remove('bg-background', 'text-foreground', 'shadow-sm');
        loginTab.classList.add('text-muted-foreground');
        
        signupContent.classList.remove('hidden');
        loginContent.classList.add('hidden');
    });

    // Login form
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        const role = document.querySelector('input[name="role"]:checked').value;
        
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&role=${encodeURIComponent(role)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred', 'error');
        });
    });

    // Signup form
    document.getElementById('signupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('signupName').value;
        const email = document.getElementById('signupEmail').value;
        const mobile = document.getElementById('signupMobile').value;
        const address = document.getElementById('signupAddress').value;
        const password = document.getElementById('signupPassword').value;
        const confirmPassword = document.getElementById('signupConfirm').value;
        
        if (password !== confirmPassword) {
            showToast('Passwords do not match', 'error');
            return;
        }
        
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=signup&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&mobile=${encodeURIComponent(mobile)}&address=${encodeURIComponent(address)}&password=${encodeURIComponent(password)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Reset form
                document.getElementById('signupForm').reset();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred', 'error');
        });
    });
});
</script>