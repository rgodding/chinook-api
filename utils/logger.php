<?php
class Logger 
{
    const LOG_FILE = BASE_PATH . '/logs/app.log';
    const ERROR_LOG_FILE = BASE_PATH . '/logs/error.log';

    public static function LogInfo($message, $entity = '')
    {
        self::log('INFO', $entity, self::LOG_FILE, $message);
    }
    public static function LogError($message, $entity = '')
    {
        self::log('ERROR', $entity, self::ERROR_LOG_FILE, $message);
    }


    // $message, $entity = '', $responseCode = 200, $level = 'INFO'
    private static function log(string $level = 'INFO', string $entity = '', string $filePath, string $message = '')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] [$entity] $message" . PHP_EOL;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, ''); // Create the file if it doesn't exist
        }
        file_put_contents($filePath, $logEntry, FILE_APPEND);
    }
}