<?php

class DBCredentials
{
    protected string $host;
    protected string $dbname;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        $dotenvPath = BASE_PATH . '../../.env';
        $dotenvPathLocal = BASE_PATH . '/.env';
        if (!file_exists($dotenvPath) && !file_exists($dotenvPathLocal)) {
            throw new Exception('.env file not found');
        }

        $this->host = $env['DB_HOST'] ?? throw new Exception('DB_HOST missing in .env');
        $this->dbname = $env['DB_NAME'] ?? throw new Exception('DB_NAME missing in .env');
        $this->user = $env['DB_USER'] ?? throw new Exception('DB_USER missing in .env');
        $this->password = $env['DB_PASSWORD'] ?? throw new Exception('DB_PASSWORD missing in .env');
    }
}
