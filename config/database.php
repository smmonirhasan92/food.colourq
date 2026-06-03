<?php
/**
 * Database Connection and Initialization
 * Food Delivery & Real-Time Notification System
 */

require_once __DIR__ . '/constants.php';

class Database {
    private static ?PDO $instance = null;

    /**
     * Get the database connection instance (Singleton)
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                // Auto-migration: If SQLite database is in Bengali, delete it to trigger English re-seeding
                if (DB_TYPE === 'sqlite') {
                    $dbFile = DB_SQLITE_FILE;
                    if (file_exists($dbFile)) {
                        try {
                            $tempPdo = new PDO("sqlite:" . $dbFile);
                            $tempPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt = $tempPdo->query("SELECT name FROM menu_items LIMIT 1");
                            if ($stmt) {
                                $row = $stmt->fetch();
                                // If the name does NOT contain English terms like 'Truffle' or 'Garlic', it is in Bengali. Clear it.
                                if ($row && strpos($row['name'], 'Truffle') === false && strpos($row['name'], 'Garlic') === false) {
                                    unset($stmt);
                                    unset($tempPdo);
                                    unlink($dbFile);
                                }
                            }
                        } catch (Exception $migrationEx) {
                            // Ignore any errors during migration check
                        }
                    }
                }

                if (DB_TYPE === 'sqlite') {
                    self::$instance = self::connectSQLite();
                } else {
                    self::$instance = self::connectMySQL();
                }
            } catch (PDOException $e) {
                // In production, log the error and show a generic message.
                // In development, show the detailed error.
                error_log("Database connection error: " . $e->getMessage());
                header('Content-Type: application/json', true, 500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed: ' . (APP_ENV === 'development' ? $e->getMessage() : 'Internal Server Error.')
                ]);
                exit;
            }
        }
        return self::$instance;
    }

    /**
     * Connect to SQLite and auto-initialize if DB file doesn't exist
     */
    private static function connectSQLite(): PDO {
        $dbFile = DB_SQLITE_FILE;
        $dbDir = dirname($dbFile);
        
        // Ensure directory exists
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        $dbExists = file_exists($dbFile);

        $dsn = "sqlite:" . $dbFile;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, null, null, $options);
        
        // Enable Foreign Keys in SQLite
        $pdo->exec('PRAGMA foreign_keys = ON;');

        // Auto-initialize if database did not exist or is empty
        if (!$dbExists || filesize($dbFile) === 0) {
            self::initializeDatabase($pdo, true);
        }

        return $pdo;
    }

    /**
     * Connect to MySQL
     */
    private static function connectMySQL(): PDO {
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=%s",
            DB_MYSQL_HOST,
            DB_MYSQL_PORT,
            DB_MYSQL_DB,
            DB_MYSQL_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_MYSQL_CHARSET
        ];

        $pdo = new PDO($dsn, DB_MYSQL_USER, DB_MYSQL_PASS, $options);

        // Auto-initialize MySQL tables if they don't exist yet
        try {
            $pdo->query("SELECT 1 FROM menu_items LIMIT 1");
        } catch (PDOException $e) {
            // Table doesn't exist, trigger auto-initialization for MySQL
            self::initializeDatabase($pdo, false);
        }

        return $pdo;
    }

    /**
     * Initialize the database using schema.sql and sample-data.sql
     */
    public static function initializeDatabase(PDO $pdo, bool $isSqlite): void {
        $schemaPath = BASE_PATH . '/database/schema.sql';
        $sampleDataPath = BASE_PATH . '/database/sample-data.sql';

        if (!file_exists($schemaPath)) {
            throw new Exception("Database schema file not found at " . $schemaPath);
        }

        try {
            $pdo->beginTransaction();

            // Execute schema
            self::executeSqlFile($pdo, $schemaPath, $isSqlite);

            // Execute sample data if it exists
            if (file_exists($sampleDataPath)) {
                self::executeSqlFile($pdo, $sampleDataPath, $isSqlite);
            }

            $pdo->commit();
            error_log("Database auto-initialization completed successfully.");
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Database auto-initialization failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Execute SQL statements from a file
     */
    private static function executeSqlFile(PDO $pdo, string $filePath, bool $isSqlite): void {
        $sql = file_get_contents($filePath);
        if ($sql === false) {
            throw new Exception("Unable to read SQL file: " . $filePath);
        }

        // Apply SQLite compatibility conversions if needed
        if ($isSqlite) {
            // Replace AUTO_INCREMENT with AUTOINCREMENT
            $sql = str_replace('AUTO_INCREMENT', 'AUTOINCREMENT', $sql);
        }

        // Simple parser to separate queries by semicolon.
        // Comments are ignored and SQL blocks are parsed.
        $queries = explode(';', $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            
            if (empty($query)) {
                continue;
            }

            // Strip out single-line comments inside statements if any
            $lines = explode("\n", $query);
            $cleanLines = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (strpos($trimmed, '--') !== 0) {
                    $cleanLines[] = $line;
                }
            }
            $query = trim(implode("\n", $cleanLines));

            if (empty($query)) {
                continue;
            }

            // Execute the query
            $pdo->exec($query);
        }
    }
}
