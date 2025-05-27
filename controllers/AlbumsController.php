<?php
require_once 'BaseController.php';
require_once BASE_PATH . '/classes/Albums.php';

class AlbumsController extends BaseController
{
    public function initialize($urlData)
    {
        $this->model = new Albums();
        parent::initialize($urlData);
    }

    public function handleGet()
    {
        switch (count($this->params)) {
            case 0:
                if (!empty($this->query[Constants::QUERY_SEARCH])){
                    $searchQuery = $this->query[Constants::QUERY_SEARCH];
                    $response = $this->model->search($searchQuery);
                    $this->sendResponse($response);
                }
                $response = $this->model->list();
                $this->sendResponse($response);
                break;
            case 1:
                $id = $this->params[0];
                $this->validateId($id);
                $response = $this->model->get((int)$id);
                $this->sendResponse($response);
                break;
            case 2: 
                $id = $this->params[0];
                $this->validateId($id);
                $action = $this->params[1];
                if ($action === Constants::ACTION_TRACKS) {
                    $album = $this->model->get((int)$id);
                    if ($album[ApiResponse::POS_STATUS] !== ApiResponse::STATUS_SUCCESS) {
                        $this->sendResponse($album);
                    }
                    $response = $this->model->getTracks((int)$id);
                    $this->sendResponse($response);
                } else {
                    echo "Invalid action: $action\n";
                    $this->sendErrorResponse('Invalid action', 400);
                    exit;
                }
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters', 400);
                exit;
        }
    }
    public function handlePost()
    {
        switch (count($this->params)) {
            case 0:
                $data = $this->getRequestBody();
                $title = $data[Constants::JSON_TITLE] ?? null;
                $artistId = $data[Constants::JSON_ARTIST_ID] ?? null;
                if (is_null($title) || is_null($artistId)) {
                    $this->sendErrorResponse('Missing required parameters', 400);
                    return;
                }
                $response = $this->model->create($title, $artistId);
                $this->sendResponse($response);
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters for POST', 400);
                break;
        }
    }

    public function handlePut()
    {
        switch (count($this->params)) {
            case 1:
                $id = $this->params[0];
                $this->validateId($id);
                $albumData = $this->model->getById((int)$id);
                if ($albumData[ApiResponse::POS_STATUS] !== ApiResponse::STATUS_SUCCESS) {
                    $this->sendResponse($albumData);
                }
                $oldAlbum = $albumData[ApiResponse::POS_DATA];
                $data = $this->getRequestBody();
                $title = $data[Constants::JSON_TITLE] ?? $oldAlbum['Title'];
                $artistId = $data[Constants::JSON_ARTIST_ID] ?? $oldAlbum['ArtistId'];
                $response = $this->model->update((int)$id, $title, (int)$artistId);
                $this->sendResponse($response);
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters for PUT', 400);
                break;
        }
    }

    public function handleDelete()
    {
        switch (count($this->params)) {
            case 1:
                $id = $this->params[0];
                $this->validateId($id);
                $response = $this->model->delete((int)$id);
                $this->sendResponse($response);
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters for DELETE', 400);
                break;
        }
    }
}
