<?php

function logMessage($message, $level = 'INFO')
{
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;

    // Log to a file
    if($level === 'ERROR'){
        $logFile = BASE_PATH . '/logs/error.log';
    } else {
        $logFile = BASE_PATH . '/logs/app.log';
    }
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

function logError($message)
{
    logMessage($message, 'ERROR');
}