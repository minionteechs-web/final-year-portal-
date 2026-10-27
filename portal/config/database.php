<?php
/**
 * Database configuration — dual backend: mysqli (preferred) or PDO fallback.
 * University of Nigeria, Nsukka — Lecturer Overview Portal
 *
 * If the `mysqli` PHP extension is not installed, this transparently falls back
 * to PDO (pdo_mysql). All calling code continues to use the same `db()` API
 * (prepare, bind_param, execute, get_result, fetch_assoc, fetch_all, etc.).
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'lecturer_overview');

/* Define MYSQLI_ASSOC for PDO fallback (normally defined by the mysqli ext) */
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}

/**
 * Result wrapper — provides fetch_assoc() and fetch_all() for both
 * mysqli_result and PDOStatement backends.
 */
class DbResult
{
    /** @var array<array<string,mixed>> */
    private array $rows = [];
    private int $pos = 0;

    /** @param array<array<string,mixed>> $rows */
    public function __construct(array $rows = [])
    {
        $this->rows = $rows;
    }

    public function fetch_assoc(): ?array
    {
        if ($this->pos < count($this->rows)) {
            return $this->rows[$this->pos++];
        }
        return null;
    }

    /**
     * @param int $mode Ignored — always returns associative arrays.
     * @return array<array<string,mixed>>
     */
    public function fetch_all(int $mode = MYSQLI_ASSOC): array
    {
        return $this->rows;
    }

    public function num_rows(): int
    {
        return count($this->rows);
    }
}

/**
 * Statement wrapper — provides bind_param(), execute(), get_result(),
 * close(), insert_id for both mysqli_stmt and PDOStatement backends.
 */
class DbStatement
{
    private $stmt;
    private bool $isPdo;
    private ?DbConnection $pdoConn = null;
    private ?int $lastInsertId = null;

    public function __construct($stmt, bool $isPdo = false, ?DbConnection $pdoConn = null)
    {
        $this->stmt = $stmt;
        $this->isPdo = $isPdo;
        $this->pdoConn = $pdoConn;
    }

    /**
     * @param string $types e.g. 'sssssi'
     * @param mixed ...$vars
     */
    public function bind_param(string $types, mixed ...$vars): bool
    {
        if ($this->isPdo) {
            // PDO uses 1-based positional parameters for ? placeholders
            foreach ($vars as $i => $var) {
                $this->stmt->bindValue($i + 1, $var);
            }
            return true;
        }
        return $this->stmt->bind_param($types, ...$vars);
    }

    public function execute(): bool
    {
        $result = $this->stmt->execute();
        if ($this->isPdo && $this->pdoConn !== null) {
            $this->lastInsertId = (int) $this->pdoConn->lastInsertId();
        }
        return $result;
    }

    public function get_result(): DbResult
    {
        if ($this->isPdo) {
            $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            return new DbResult($rows);
        }
        $result = $this->stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return new DbResult($rows);
    }

    /**
     * @param int $mode Ignored — always returns associative arrays.
     * @return array<array<string,mixed>>
     */
    public function fetch_all(int $mode = MYSQLI_ASSOC): array
    {
        if ($this->isPdo) {
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->stmt->get_result()->fetch_all($mode);
    }

    public function close(): void
    {
        if ($this->isPdo) {
            $this->stmt = null;
        } else {
            $this->stmt->close();
        }
    }

    public function __get(string $name)
    {
        if ($name === 'insert_id') {
            return $this->lastInsertId ?? ($this->isPdo ? 0 : $this->stmt->insert_id);
        }
        return null;
    }
}

/**
 * Connection wrapper — provides prepare(), begin_transaction(), commit(),
 * rollback(), set_charset(), query() for both mysqli and PDO backends.
 */
class DbConnection
{
    private $conn;
    private bool $isPdo;

    public function __construct($conn, bool $isPdo = false)
    {
        $this->conn = $conn;
        $this->isPdo = $isPdo;
    }

    public function prepare(string $sql): DbStatement
    {
        if ($this->isPdo) {
            $stmt = $this->conn->prepare($sql);
            return new DbStatement($stmt, true, $this);
        }
        return new DbStatement($this->conn->prepare($sql), false);
    }

    public function begin_transaction(): void
    {
        if ($this->isPdo) {
            $this->conn->beginTransaction();
        } else {
            $this->conn->begin_transaction();
        }
    }

    public function commit(): void
    {
        if ($this->isPdo) {
            $this->conn->commit();
        } else {
            $this->conn->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->isPdo) {
            $this->conn->rollBack();
        } else {
            $this->conn->rollback();
        }
    }

    public function set_charset(string $charset): void
    {
        if ($this->isPdo) {
            $this->conn->exec("SET NAMES $charset");
        } else {
            $this->conn->set_charset($charset);
        }
    }

    public function lastInsertId(): int
    {
        if ($this->isPdo) {
            return (int) $this->conn->lastInsertId();
        }
        return (int) $this->conn->insert_id;
    }

    public function query(string $sql): DbResult
    {
        if ($this->isPdo) {
            $stmt = $this->conn->query($sql);
            if ($stmt === false) {
                return new DbResult([]);
            }
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return new DbResult($rows);
        }
        $result = $this->conn->query($sql);
        if ($result === false) {
            return new DbResult([]);
        }
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return new DbResult($rows);
    }

    public function __get(string $name)
    {
        if ($name === 'connect_error') {
            return $this->isPdo ? null : ($this->conn->connect_error ?? null);
        }
        return null;
    }

    public function close(): void
    {
        if (!$this->isPdo && $this->conn instanceof \mysqli) {
            $this->conn->close();
        }
    }
}

/**
 * @return DbConnection
 */
function db(): DbConnection
{
    static $conn = null;

    if ($conn instanceof DbConnection) {
        return $conn;
    }

    // ── Try mysqli first ──────────────────────────────────────────────
    if (class_exists('\mysqli')) {
        try {
            if (function_exists('mysqli_report')) {
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            }
            $mysqli = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($mysqli->connect_error) {
                throw new \Exception((string) $mysqli->connect_error);
            }
            $mysqli->set_charset('utf8mb4');
            $conn = new DbConnection($mysqli, false);
            return $conn;
        } catch (\Throwable $e) {
            // Fall through to PDO
        }
    }

    // ── Try PDO fallback ──────────────────────────────────────────────
    if (class_exists('\PDO')) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new \PDO($dsn, DB_USER, DB_PASS, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $conn = new DbConnection($pdo, true);
            return $conn;
        } catch (\Throwable $e) {
            http_response_code(500);
            die('Database connection failed. Neither mysqli nor PDO is available. Install php-mysqli or pdo_mysql extension.');
        }
    }

    http_response_code(500);
    die('Database connection failed. Neither mysqli nor PDO extension is available on this server.');
}
