<?php
/**
 * Live Deployment & Database Diagnostic Page
 * Food ColourQ - Premium Auto-Initialization Verification
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

$connectionSuccess = false;
$errorMessage = "";
$tablesInfo = [];
$dbType = DB_TYPE;
$dbName = DB_MYSQL_DB;

try {
    // Attempt connection (will automatically trigger MySQL schema generation if empty)
    $db = Database::getConnection();
    $connectionSuccess = true;

    // Database Migration: Create delivery_men table and add delivery_man_id to orders
    if (DB_TYPE === 'sqlite') {
        $db->exec("CREATE TABLE IF NOT EXISTS delivery_men (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            status VARCHAR(20) DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $dmCount = $db->query("SELECT COUNT(*) FROM delivery_men")->fetchColumn();
        if ($dmCount == 0) {
            $db->exec("INSERT INTO delivery_men (name, phone, status) VALUES 
                ('Rahat Khan', '01712345678', 'available'),
                ('Sumon Mia', '01812345678', 'available'),
                ('Kamal Hossain', '01912345678', 'available')");
        }
        
        $colCheck = $db->query("PRAGMA table_info(orders)")->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!in_array('delivery_man_id', $colCheck)) {
            $db->exec("ALTER TABLE orders ADD COLUMN delivery_man_id INTEGER NULL");
        }
    } else {
        $db->exec("CREATE TABLE IF NOT EXISTS delivery_men (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            status VARCHAR(20) DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $dmCount = $db->query("SELECT COUNT(*) FROM delivery_men")->fetchColumn();
        if ($dmCount == 0) {
            $db->exec("INSERT INTO delivery_men (name, phone, status) VALUES 
                ('Rahat Khan', '01712345678', 'available'),
                ('Sumon Mia', '01812345678', 'available'),
                ('Kamal Hossain', '01912345678', 'available')");
        }
        
        $colsQuery = $db->query("SHOW COLUMNS FROM orders LIKE 'delivery_man_id'");
        $colExists = $colsQuery->fetch();
        if (!$colExists) {
            $db->exec("ALTER TABLE orders ADD COLUMN delivery_man_id INT NULL");
        }
    }

    // Cost Price Migration: Add cost_price to menu_items table if not exists
    if (DB_TYPE === 'sqlite') {
        $colCheck = $db->query("PRAGMA table_info(menu_items)")->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!in_array('cost_price', $colCheck)) {
            $db->exec("ALTER TABLE menu_items ADD COLUMN cost_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00");
        }
    } else {
        $colsQuery = $db->query("SHOW COLUMNS FROM menu_items LIKE 'cost_price'");
        $colExists = $colsQuery->fetch();
        if (!$colExists) {
            $db->exec("ALTER TABLE menu_items ADD COLUMN cost_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00");
        }
    }

    // Category Migration: Create menu_categories table
    if (DB_TYPE === 'sqlite') {
        $db->exec("CREATE TABLE IF NOT EXISTS menu_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE,
            slug VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $catCount = $db->query("SELECT COUNT(*) FROM menu_categories")->fetchColumn();
        if ($catCount == 0) {
            $db->exec("INSERT INTO menu_categories (name, slug) VALUES 
                ('Starter', 'appetizer'),
                ('Best Seller', 'main'),
                ('Dessert', 'dessert'),
                ('Drink', 'drink')");
        }
    } else {
        $db->exec("CREATE TABLE IF NOT EXISTS menu_categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL UNIQUE,
            slug VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $catCount = $db->query("SELECT COUNT(*) FROM menu_categories")->fetchColumn();
        if ($catCount == 0) {
            $db->exec("INSERT INTO menu_categories (name, slug) VALUES 
                ('Starter', 'appetizer'),
                ('Best Seller', 'main'),
                ('Dessert', 'dessert'),
                ('Drink', 'drink')");
        }
    }

    // Fetch all tables in MySQL
    $tablesQuery = $db->query("SHOW TABLES");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $countQuery = $db->query("SELECT COUNT(*) FROM `$table`");
        $count = $countQuery->fetchColumn();
        $tablesInfo[] = [
            'name' => $table,
            'count' => $count
        ];
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ডেপ্লয়মেন্ট ডায়াগনস্টিক - ফুড কালারকিউ</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ea6721;
            --success: #10b981;
            --danger: #ef4444;
            --bg-dark: #0f172a;
            --bg-surface: rgba(255, 255, 255, 0.9);
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', 'Hind Siliguri', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .card {
            background: var(--bg-surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.8);
            width: 100%;
            max-width: 650px;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            text-align: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(45deg, #ea6721, #ff8343, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.5rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        .status-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            text-align: left;
            margin-bottom: 2rem;
        }

        .info-box {
            background: #ffffff;
            border: 1px solid var(--border);
            padding: 1.2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.01);
        }

        .info-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .info-val {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .tables-list {
            text-align: left;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .tables-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.5rem;
        }

        .table-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .table-name {
            font-family: monospace;
            font-weight: 600;
            color: var(--primary);
        }

        .table-count {
            font-weight: bold;
            background: #f1f5f9;
            padding: 0.15rem 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .btn-portal {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 0.9rem 2rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 8px 16px rgba(234, 103, 33, 0.25);
            transition: all 0.3s ease;
        }

        .btn-portal:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(234, 103, 33, 0.35);
        }

        .error-card {
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.15);
            border-radius: 16px;
            padding: 1.25rem;
            color: var(--danger);
            text-align: left;
            font-family: monospace;
            white-space: pre-wrap;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">ফুড কালারকিউ ডায়াগনস্টিক</div>
        
        <?php if ($connectionSuccess): ?>
            <div>
                <span class="status-badge status-success">
                    <i class="fa-solid fa-circle-check"></i> কানেকশন সফল ও ডাটাবেজ প্রস্তুত!
                </span>
            </div>

            <div class="details-grid">
                <div class="info-box">
                    <div class="info-label">ডাটাবেজ ড্রাইভার</div>
                    <div class="info-val"><?php echo strtoupper($dbType); ?></div>
                </div>
                <div class="info-box">
                    <div class="info-label">ডাটাবেজ নাম</div>
                    <div class="info-val"><?php echo htmlspecialchars($dbName); ?></div>
                </div>
            </div>

            <div class="tables-list">
                <div class="tables-title">
                    <i class="fa-solid fa-database" style="color: var(--primary);"></i> অটো-ইনিশিয়ালাইজড টেবিলসমূহ
                </div>
                <?php foreach ($tablesInfo as $tbl): ?>
                    <div class="table-row">
                        <span class="table-name"><?php echo htmlspecialchars($tbl['name']); ?></span>
                        <span class="table-count"><?php echo $tbl['count']; ?> টি রো (Rows)</span>
                    </div>
                <?php endforeach; ?>
            </div>

            <a href="customer/index.php" class="btn-portal">
                কাস্টমার পোর্টালে প্রবেশ করুন <i class="fa-solid fa-arrow-right"></i>
            </a>

        <?php else: ?>
            <div>
                <span class="status-badge status-error">
                    <i class="fa-solid fa-circle-xmark"></i> কানেকশন ব্যর্থ হয়েছে
                </span>
            </div>

            <div class="error-card">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>

            <div style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                <p><i class="fa-solid fa-circle-info"></i> অনুগ্রহ করে আপনার cPanel-এর <strong>MySQL Databases</strong> সেকশনে গিয়ে নিশ্চিত করুন যে ডাটাবেজ ইউজার এবং পাসওয়ার্ড সঠিক আছে এবং ইউজারটিকে ডাটাবেজের সাথে সকল প্রিভিলেজ (All Privileges) দিয়ে যুক্ত করা হয়েছে।</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
