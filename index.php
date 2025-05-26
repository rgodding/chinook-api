<?php
require_once 'init.php';

// Get the URL Data
$urlData = getUrlData();
echo json_encode($urlData);
// Configure the API's response headers
switch ($urlData[Constants::ENTITY]) {
    case Constants::ENTITY_ARTISTS:
        require_once BASE_PATH . '/controllers/ArtistsController.php';
        $controller = new ArtistsController($urlData);
        break;
    case Constants::ENTITY_ALBUMS:
        require_once BASE_PATH . '/controllers/AlbumsController.php';
        $controller = new AlbumsController($urlData);
        break;
    case Constants::ENTITY_TRACKS:
        require_once BASE_PATH . '/controllers/TracksController.php';
        $controller = new TracksController($urlData);
        break;
    case Constants::ENTITY_MEDIA_TYPES:
        require_once BASE_PATH . '/controllers/MediaTypesController.php';
        $controller = new MediaTypesController($urlData);
        break;
    case Constants::ENTITY_GENRES:
        require_once BASE_PATH . '/controllers/GenresController.php';
        $controller = new GenresController($urlData);
        break;
    case Constants::ENTITY_PLAYLISTS:
        require_once BASE_PATH . '/controllers/PlaylistsController.php';
        $controller = new PlaylistsController($urlData);
        break;
    default:
        echo json_encode([
            ApiResponse::POS_STATUS => ApiResponse::STATUS_ERROR,
            ApiResponse::POS_MESSAGE => 'Invalid entity specified'
        ]);
        //sendResponse(400, 'Invalid entity specified');
        exit;
}
