<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = 'localhost';
        $dbname = 'ibarako_loan_db';
        $username = 'root';
        $password = '';
        
        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Initialize database tables
function initializeDatabase() {
    $db = Database::getInstance()->getConnection();
    
    // Users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
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
    )");
    
    // Contributions table
    $db->exec("CREATE TABLE IF NOT EXISTS contributions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        member_id VARCHAR(20) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        date DATE NOT NULL,
        type ENUM('monthly', 'additional') NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (member_id) REFERENCES users(member_id) ON DELETE CASCADE
    )");
    
    // Loans table
    $db->exec("CREATE TABLE IF NOT EXISTS loans (
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
    )");
    
    // Notifications table
    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
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
    )");
    
    // System settings table
    $db->exec("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default data if tables are empty
    insertDefaultData();
}

function insertDefaultData() {
    $db = Database::getInstance()->getConnection();
    
    // Check if admin user exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        // Insert default admin
        $stmt = $db->prepare("INSERT INTO users (name, email, password, mobile, address, role, join_date) VALUES (?, ?, ?, ?, ?, 'admin', ?)");
        $stmt->execute([
            'System Administrator',
            'admin@loan.com',
            password_hash('password', PASSWORD_DEFAULT),
            '+1234567890',
            '123 Admin St',
            date('Y-m-d')
        ]);
        
        // Insert sample members
        $stmt = $db->prepare("INSERT INTO users (member_id, name, email, password, mobile, address, role, join_date) VALUES (?, ?, ?, ?, ?, ?, 'member', ?)");
        
        $stmt->execute([
            'MBR-0001',
            'John Doe',
            'john@email.com',
            password_hash('password', PASSWORD_DEFAULT),
            '+1234567891',
            '456 Member St',
            '2024-01-15'
        ]);
        
        $stmt->execute([
            'MBR-0002',
            'Jane Smith',
            'jane@email.com',
            password_hash('password', PASSWORD_DEFAULT),
            '+1234567892',
            '789 Member Ave',
            '2024-02-01'
        ]);
        
        // Insert sample contributions
        $stmt = $db->prepare("INSERT INTO contributions (member_id, amount, date, type, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['MBR-0001', 500.00, '2024-01-15', 'monthly', 'Monthly contribution']);
        $stmt->execute(['MBR-0001', 200.00, '2024-01-20', 'additional', 'Additional savings']);
        
        // Insert sample loan
        $stmt = $db->prepare("INSERT INTO loans (member_id, amount, term_months, interest_rate, status, application_date, approval_date, start_date, monthly_payment, payments_made, total_payments, next_payment_date, agreement_signed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'MBR-0001', 10000.00, 6, 2.00, 'active', '2024-01-20', '2024-01-22', '2024-02-01', 1700.00, 4, 12, '2024-04-15', true
        ]);
        
        // Insert sample notification
        $stmt = $db->prepare("INSERT INTO notifications (type, member_id, member_name, message, date, data) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'loan_application',
            'MBR-0002',
            'Jane Smith',
            'New loan application for $15,000',
            '2024-03-15',
            json_encode(['amount' => 15000, 'termMonths' => 12])
        ]);
        
        // Insert default interest rate
        $stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('interest_rate', '2.0')");
        $stmt->execute();
    }
}

// Initialize database on first load
initializeDatabase();
?>