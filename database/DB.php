<?php

require_once BASE_PATH . '/database/DBCredentials.php';

Class DB extends DBCredentials
{
    protected ?PDO $pdo;

    public function __construct()
    {
        parent::__construct(); 
        $deployment = $this->deployment;
        if($deployment === 'Render'){
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        } else {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        }
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // Opret PDO-forbindelse med de valgte credentials og DSN
        $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}

?>