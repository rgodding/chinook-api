<?php

class DBCredentials
{
    protected string $deployment;
    protected string $port;
    protected string $host;
    protected string $dbname;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        // Her sættes alle properties, så de er initialiserede
        $this->deployment = getenv('DB_DEPLOYMENT') ?: 'local'; // Default to 'local' if not set
        $this->port = getenv('DB_PORT') ?: '3306'; // Default MySQL port
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'chinook_autoincrement';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: 'Skejs123';

    }
}