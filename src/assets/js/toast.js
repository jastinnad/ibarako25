// Toast notification system

let toastContainer = null;

// Initialize toast container
document.addEventListener('DOMContentLoaded', function() {
    toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
});

// Show toast notification
function showToast(message, type = 'info', duration = 5000) {
    if (!toastContainer) {
        console.warn('Toast container not found');
        return;
    }

    const toast = createToastElement(message, type);
    toastContainer.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 100);

    // Auto remove
    setTimeout(() => {
        removeToast(toast);
    }, duration);

    // Add click to dismiss
    const closeButton = toast.querySelector('.toast-close');
    if (closeButton) {
        closeButton.addEventListener('click', () => removeToast(toast));
    }
}

// Create toast element
function createToastElement(message, type) {
    const toast = document.createElement('div');
    toast.className = `
        flex items-center justify-between
        max-w-sm w-full
        bg-white border border-border rounded-lg shadow-lg
        p-4 
        transform transition-all duration-300 ease-in-out
        translate-x-full opacity-0
        ${getToastTypeClasses(type)}
    `;

    const icon = getToastIcon(type);
    
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                ${icon}
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-foreground">${message}</p>
            </div>
        </div>
        <button class="toast-close flex-shrink-0 ml-3 text-muted-foreground hover:text-foreground transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;

    // Initialize Lucide icons in the toast
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons(toast);
        }
    }, 0);

    return toast;
}

// Get toast type classes
function getToastTypeClasses(type) {
    switch (type) {
        case 'success':
            return 'border-green-200';
        case 'error':
            return 'border-red-200';
        case 'warning':
            return 'border-orange-200';
        case 'info':
        default:
            return 'border-blue-200';
    }
}

// Get toast icon
function getToastIcon(type) {
    switch (type) {
        case 'success':
            return '<div class="w-5 h-5 text-green-600"><i data-lucide="check-circle" class="w-5 h-5"></i></div>';
        case 'error':
            return '<div class="w-5 h-5 text-red-600"><i data-lucide="x-circle" class="w-5 h-5"></i></div>';
        case 'warning':
            return '<div class="w-5 h-5 text-orange-600"><i data-lucide="alert-triangle" class="w-5 h-5"></i></div>';
        case 'info':
        default:
            return '<div class="w-5 h-5 text-blue-600"><i data-lucide="info" class="w-5 h-5"></i></div>';
    }
}

// Remove toast
function removeToast(toast) {
    if (!toast || !toast.parentNode) return;

    // Animate out
    toast.classList.remove('translate-x-0', 'opacity-100');
    toast.classList.add('translate-x-full', 'opacity-0');

    // Remove from DOM after animation
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Toast convenience functions
function showSuccessToast(message, duration) {
    showToast(message, 'success', duration);
}

function showErrorToast(message, duration) {
    showToast(message, 'error', duration);
}

function showWarningToast(message, duration) {
    showToast(message, 'warning', duration);
}

function showInfoToast(message, duration) {
    showToast(message, 'info', duration);
}