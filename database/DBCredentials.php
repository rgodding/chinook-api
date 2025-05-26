<?php

require_once BASE_PATH . '/utils/loadEnv.php';


class DBCredentials 
{
    protected string $host;
    protected string $dbname;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        $envFile = BASE_PATH . '/.env';
        loadEnv($envFile);
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'chinook_autoincrement';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: 'Skejs123';
    }
}
/* 
class DBCredentials
{
    protected string $host = 'localhost';
    protected string $dbname = 'chinook_autoincrement';
    protected string $user = 'root';
    protected string $password = 'Skejs123';
}
 */