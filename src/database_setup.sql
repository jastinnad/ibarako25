-- ============================================
-- Loan Management System Database Setup
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS loan_system;
USE loan_system;

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(20) UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    role ENUM('admin', 'member') NOT NULL DEFAULT 'member',
    join_date DATE NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table: contributions
-- ============================================
CREATE TABLE contributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    type ENUM('monthly', 'additional') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(member_id) ON DELETE CASCADE
);

-- ============================================
-- Table: loans
-- ============================================
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    term_months INT NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    status ENUM('pending', 'approved', 'active', 'completed', 'rejected') NOT NULL DEFAULT 'pending',
    application_date DATE NOT NULL,
    approval_date DATE NULL,
    start_date DATE NULL,
    monthly_payment DECIMAL(10,2) NOT NULL,
    payments_made INT DEFAULT 0,
    total_payments INT NOT NULL,
    next_payment_date DATE NULL,
    agreement_signed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(member_id) ON DELETE CASCADE
);

-- ============================================
-- Table: notifications
-- ============================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('loan_application', 'profile_update', 'contribution') NOT NULL,
    member_id VARCHAR(20) NOT NULL,
    member_name VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(member_id) ON DELETE CASCADE
);

-- ============================================
-- Table: system_settings
-- ============================================
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- Insert Default Data
-- ============================================

-- Insert default admin user
INSERT INTO users (name, email, password, mobile, address, role, join_date) VALUES 
('System Administrator', 'admin@loan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567890', '123 Admin St', 'admin', '2024-01-01');

-- Insert sample members
INSERT INTO users (member_id, name, email, password, mobile, address, role, join_date) VALUES 
('MBR-0001', 'John Doe', 'john@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567891', '456 Member St', 'member', '2024-01-15'),
('MBR-0002', 'Jane Smith', 'jane@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567892', '789 Member Ave', 'member', '2024-02-01'),
('MBR-0003', 'Mike Johnson', 'mike@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567893', '321 Sample Rd', 'member', '2024-02-15'),
('MBR-0004', 'Sarah Wilson', 'sarah@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567894', '654 Demo St', 'member', '2024-03-01');

-- Insert sample contributions
INSERT INTO contributions (member_id, amount, date, type, description) VALUES 
('MBR-0001', 500.00, '2024-01-15', 'monthly', 'Monthly contribution'),
('MBR-0001', 200.00, '2024-01-20', 'additional', 'Additional savings'),
('MBR-0001', 500.00, '2024-02-15', 'monthly', 'Monthly contribution'),
('MBR-0001', 500.00, '2024-03-15', 'monthly', 'Monthly contribution'),
('MBR-0002', 300.00, '2024-02-01', 'monthly', 'Monthly contribution'),
('MBR-0002', 300.00, '2024-03-01', 'monthly', 'Monthly contribution'),
('MBR-0002', 150.00, '2024-03-10', 'additional', 'Extra savings'),
('MBR-0003', 400.00, '2024-02-15', 'monthly', 'Monthly contribution'),
('MBR-0003', 400.00, '2024-03-15', 'monthly', 'Monthly contribution');

-- Insert sample loans
INSERT INTO loans (member_id, amount, term_months, interest_rate, status, application_date, approval_date, start_date, monthly_payment, payments_made, total_payments, next_payment_date, agreement_signed) VALUES 
('MBR-0001', 10000.00, 6, 2.00, 'active', '2024-01-20', '2024-01-22', '2024-02-01', 1700.00, 4, 12, '2024-04-15', TRUE),
('MBR-0003', 5000.00, 3, 2.00, 'completed', '2024-01-10', '2024-01-12', '2024-01-15', 850.00, 6, 6, NULL, TRUE);

-- Insert pending loan application
INSERT INTO loans (member_id, amount, term_months, interest_rate, status, application_date, monthly_payment, payments_made, total_payments, agreement_signed) VALUES 
('MBR-0002', 15000.00, 12, 2.00, 'pending', '2024-03-15', 1275.00, 0, 24, FALSE);

-- Insert sample notifications
INSERT INTO notifications (type, member_id, member_name, message, date, data) VALUES 
('loan_application', 'MBR-0002', 'Jane Smith', 'New loan application for $15,000', '2024-03-15', '{"amount": 15000, "termMonths": 12}'),
('profile_update', 'MBR-0004', 'Sarah Wilson', 'Profile update request - Mobile: +1234567895, Email: sarah.new@email.com', '2024-03-10', '{"mobile": "+1234567895", "email": "sarah.new@email.com"}');

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value) VALUES 
('interest_rate', '2.0'),
('system_name', 'LoanSystem Financial Services'),
('max_loan_amount', '50000'),
('min_loan_amount', '100'),
('max_loan_term', '24'),
('min_loan_term', '3');

-- ============================================
-- Create indexes for better performance
-- ============================================
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_member_id ON users(member_id);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_contributions_member_id ON contributions(member_id);
CREATE INDEX idx_contributions_date ON contributions(date);
CREATE INDEX idx_loans_member_id ON loans(member_id);
CREATE INDEX idx_loans_status ON loans(status);
CREATE INDEX idx_notifications_status ON notifications(status);
CREATE INDEX idx_notifications_member_id ON notifications(member_id);

-- ============================================
-- Show created tables and record counts
-- ============================================
SHOW TABLES;

SELECT 'Users' as Table_Name, COUNT(*) as Record_Count FROM users
UNION ALL
SELECT 'Contributions', COUNT(*) FROM contributions
UNION ALL
SELECT 'Loans', COUNT(*) FROM loans
UNION ALL
SELECT 'Notifications', COUNT(*) FROM notifications
UNION ALL
SELECT 'System Settings', COUNT(*) FROM system_settings;

-- ============================================
-- Display sample data to verify
-- ============================================
SELECT 'Sample Users:' as Info;
SELECT member_id, name, email, role, status FROM users WHERE role = 'member';

SELECT 'Sample Admin:' as Info;
SELECT name, email, role FROM users WHERE role = 'admin';

SELECT 'Active Loans:' as Info;
SELECT l.member_id, u.name, l.amount, l.status, l.payments_made, l.total_payments 
FROM loans l 
JOIN users u ON l.member_id = u.member_id 
WHERE l.status IN ('active', 'pending');

-- ============================================
-- Database Setup Complete
-- ============================================