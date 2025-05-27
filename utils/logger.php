<?php

function logMessage($message, $level = 'INFO')
{
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;

    // Log to a file
    if($level === 'ERROR'){
        $logFile = BASE_PATH . '/logs/error.log';
        if (!file_exists($logFile)) {
            file_put_contents($logFile, ''); // Create the file if it doesn't exist
        }
    } else {
        $logFile = BASE_PATH . '/logs/app.log';
        if (!file_exists($logFile)) {
            file_put_contents($logFile, ''); // Create the file if it doesn't exist
        }
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
function logError($message)
{
    logMessage($message, 'ERROR');
}