// Global JavaScript functions

// Alert functions
function closeAlert() {
    const alert = document.getElementById('alert');
    if (alert) {
        alert.style.display = 'none';
    }
}

// Auto close alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alert = document.getElementById('alert');
    if (alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    }
});

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
});

// Sidebar functions
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebar && mainContent) {
        sidebar.classList.toggle('open');
        mainContent.classList.toggle('sidebar-collapsed');
    }
}

// Tab functions
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tabs-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    
    // Remove active class from all tab links
    tablinks = document.getElementsByClassName("tabs-trigger");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    
    // Show the selected tab content and add active class to the button
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'var(--destructive)';
            isValid = false;
        } else {
            field.style.borderColor = 'var(--border)';
        }
    });
    
    return isValid;
}

// Format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Calculate loan payments
function calculateLoanPayments() {
    const amount = parseFloat(document.getElementById('loanAmount')?.value || 0);
    const termMonths = parseInt(document.getElementById('termMonths')?.value || 6);
    const interestRate = parseFloat(document.getElementById('interestRate')?.value || 2);
    
    if (amount > 0 && termMonths > 0) {
        const totalPayments = termMonths * 2; // Bi-monthly payments
        const interestPerPayment = interestRate / 2 / 100;
        const principalPerPayment = amount / totalPayments;
        const interestAmount = amount * interestPerPayment;
        const totalPerPayment = principalPerPayment + interestAmount;
        
        // Update display elements
        const totalPaymentsElement = document.getElementById('totalPayments');
        const paymentAmountElement = document.getElementById('paymentAmount');
        
        if (totalPaymentsElement) {
            totalPaymentsElement.textContent = totalPayments;
        }
        
        if (paymentAmountElement) {
            paymentAmountElement.textContent = formatCurrency(totalPerPayment);
        }
    }
}

// Progress bar animation
function animateProgressBar(elementId, percentage) {
    const progressBar = document.getElementById(elementId);
    if (progressBar) {
        setTimeout(() => {
            progressBar.style.width = percentage + '%';
        }, 100);
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Copied to clipboard!', 'success');
    }, function(err) {
        console.error('Could not copy text: ', err);
        showToast('Failed to copy', 'error');
    });
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Confirm dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Print function
function printPage() {
    window.print();
}

// Search functionality
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toUpperCase();
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) { // Skip header row
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Sort table
function sortTable(tableId, column, type = 'string') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.getElementsByTagName('tr'));
    
    rows.sort((a, b) => {
        const aVal = a.getElementsByTagName('td')[column].textContent.trim();
        const bVal = b.getElementsByTagName('td')[column].textContent.trim();
        
        if (type === 'number') {
            return parseFloat(aVal) - parseFloat(bVal);
        } else if (type === 'date') {
            return new Date(aVal) - new Date(bVal);
        } else {
            return aVal.localeCompare(bVal);
        }
    });
    
    // Clear tbody and append sorted rows
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

// Auto-logout after inactivity
let inactivityTimer;
const INACTIVITY_TIME = 30 * 60 * 1000; // 30 minutes

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        if (confirm('You have been inactive for 30 minutes. Do you want to stay logged in?')) {
            resetInactivityTimer();
        } else {
            window.location.href = '/logout.php';
        }
    }, INACTIVITY_TIME);
}

// Reset timer on user activity
document.addEventListener('mousedown', resetInactivityTimer);
document.addEventListener('mousemove', resetInactivityTimer);
document.addEventListener('keypress', resetInactivityTimer);
document.addEventListener('scroll', resetInactivityTimer);
document.addEventListener('touchstart', resetInactivityTimer);

// Initialize timer
resetInactivityTimer();

// Initialize sidebar menu active state
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const menuButtons = document.querySelectorAll('.sidebar-menu-button');
    
    menuButtons.forEach(button => {
        const href = button.getAttribute('href');
        if (href && currentPath.includes(href)) {
            button.classList.add('active');
        }
    });
});

// Form submission with loading state
function submitFormWithLoading(formId, buttonId) {
    const form = document.getElementById(formId);
    const button = document.getElementById(buttonId);
    
    if (!form || !button) return;
    
    if (!validateForm(formId)) {
        showToast('Please fill in all required fields', 'error');
        return false;
    }
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    // Re-enable button after form submission
    setTimeout(() => {
        button.disabled = false;
        button.innerHTML = button.getAttribute('data-original-text') || 'Submit';
    }, 2000);
    
    return true;
}

// Initialize tooltips (if needed)
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeTooltips();
    
    // Store original button text for loading states
    const buttons = document.querySelectorAll('button[type="submit"]');
    buttons.forEach(button => {
        button.setAttribute('data-original-text', button.textContent);
    });
});