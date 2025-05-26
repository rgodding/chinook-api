<?php

// Get ressource from the URL
function getUrlData() {
    // Get the current URL
    $url = $_SERVER['REQUEST_URI'];
    // Remove the query string if it exists
    $url = strtok($url, '?');
    // Split the URL into parts
    $urlParts = explode('/', trim($url, '/'));
    // Remove first part
    array_shift($urlParts);

    $urlData = [
        Constants::ENTITY => $urlParts[0] ?? null,
        Constants::METHOD => $_SERVER['REQUEST_METHOD'],
        Constants::PARAMS => []
    ];

    // Fill params for each after the controller
    for ($i = 1; $i < count($urlParts); $i++) {
        $urlData['params'][] = $urlParts[$i];
    };

    return $urlData;
}