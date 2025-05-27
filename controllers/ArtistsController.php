<?php
require_once 'BaseController.php';
require_once BASE_PATH . '/classes/Artists.php';

class ArtistsController extends BaseController
{
    public function initialize($urlData)
    {
        $this->model = new Artists();
        parent::initialize($urlData);
    }

    public function handleGet()
    {
        switch (count($this->params)) {
            // Get All Artists, or Search Artists
            case 0:
                // Check for query
                if (!empty($this->query[Constants::QUERY_SEARCH])){
                    $searchQuery = $this->query[Constants::QUERY_SEARCH];
                    $response = $this->model->search($searchQuery);
                    $this->sendResponse($response);
                }
                $response = $this->model->list();
                $this->sendResponse($response);
                break;
            // Get Artist by ID
            case 1:
                $id = $this->params[0];
                $this->validateId($id);
                $response = $this->model->get((int)$id);
                $this->sendResponse($response);
                break;
            // Get Albums by Artist ID
            case 2: 
                $id = $this->params[0];
                $this->validateId($id);
                $action = $this->params[1];
                // Validate action
                if ($action === Constants::ACTION_ALBUMS) {
                    // Check if artist exists
                    $artist = $this->model->get((int)$id);
                    if ($artist[ApiResponse::POS_STATUS] !== ApiResponse::STATUS_SUCCESS) {
                        $this->sendResponse($artist);
                    }
                    // Get albums for the artist
                    $response = $this->model->getAlbums((int)$id);
                    $this->sendResponse($response);
                } else {
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
                $name = $data[Constants::JSON_NAME] ?? null;
                if (is_null($name)) {
                    $this->sendErrorResponse('Missing required parameters', 400);
                    exit;
                }
                $response = $this->model->create($name);
                $this->sendResponse($response);
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters', 400);
                exit;
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
                $this->sendErrorResponse('Invalid number of parameters', 400);
                exit;
        }
    }
}
