<?php

require_once BASE_PATH . '/database/DBCredentials.php';

Class DB extends DBCredentials
{
    protected ?PDO $pdo;

    public function __construct()
    {
        parent::__construct(); 
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            Logger::LogError('Database connection failed: ' . $e->getMessage(), 'DB');
            throw new Exception('Internal server error');
        }
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}

?>