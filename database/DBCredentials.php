<?php
require_once BASE_PATH . '/utils/parseEnvFile.php';

class DBCredentials
{
    protected string $host;
    protected string $dbname;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        $envPaths = [
            BASE_PATH . '/.env',
            BASE_PATH . '/../.env',
            BASE_PATH . '/../../.env'
        ];
        $dotenvPath = null;
        // Check if the .env file exists in any of the specified paths
        foreach ($envPaths as $path) {
            if (file_exists($path)) {
                $dotenvPath = $path;
                break;
            }
        }

        $env = parseEnvFile($dotenvPath);
        if (empty($env)) {
            Logger::LogError('No environment variables found in .env file', 'DBCredentials');
            throw new Exception('Internal server error');
        }    

        $this->host = $env['DB_HOST'] ?? throw new Exception('DB_HOST missing in .env');
        $this->dbname = $env['DB_NAME'] ?? throw new Exception('DB_NAME missing in .env');
        $this->user = $env['DB_USER'] ?? throw new Exception('DB_USER missing in .env');
        $this->password = $env['DB_PASSWORD'] ?? throw new Exception('DB_PASSWORD missing in .env');
    }
}
