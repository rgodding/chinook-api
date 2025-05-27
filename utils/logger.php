<?php

function logMessage($message, $entity, $responseCode = 200, $level = 'INFO')
{
    $timestamp = date('Y-m-d H:i:s');
    // [2025-05-28 00:04:05] [INFO] [ALBUMS][201] Album created: {"AlbumId":355,"Title":"NEW ALBUM INIT HAHA","ArtistId":2}
    $logEntry = "[$timestamp] [$level] [$entity][$responseCode] $message" . PHP_EOL;

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

// [2025-05-28 00:04:05] [INFO] [ALBUMS][201] Album created: {"AlbumId":355,"Title":"NEW ALBUM INIT HAHA","ArtistId":2}
function logInfo($message)
{
    logMessage($message, 'INFO');
}

function logError($message)
{
    logMessage($message, 'ERROR');
}