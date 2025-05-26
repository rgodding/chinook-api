<?php
require_once 'BaseController.php';
require_once BASE_PATH . '/classes/Playlists.php';

class PlaylistsController extends BaseController
{
    public function initialize($urlData)
    {
        $this->model = new Playlists();
        parent::initialize($urlData);
    }

    public function handleGet()
    {
        switch (count($this->params)) {
            case 0:
                if (!empty($this->query[Constants::QUERY_SEARCH])) {
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
                $playlistData = $this->model->get((int)$id);
                if ($playlistData[ApiResponse::POS_STATUS] !== ApiResponse::STATUS_SUCCESS) {
                    $this->sendResponse($playlistData);
                }
                $tracksData = $this->model->getTracks((int)$id);
                if ($tracksData[ApiResponse::POS_STATUS] !== ApiResponse::STATUS_SUCCESS) {
                    $this->sendResponse($tracksData);
                }
                $response = [
                    ApiResponse::POS_STATUS => ApiResponse::STATUS_SUCCESS,
                    ApiResponse::POS_DATA => [
                        'playlist' => $playlistData[ApiResponse::POS_DATA],
                        'tracks' => $tracksData[ApiResponse::POS_DATA]
                    ]
                ];
                $this->sendResponse($response);
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
            case 2:
                $playlistId = $this->params[0];
                $this->validateId($playlistId);
                $playlistData = $this->model->get((int)$playlistId);
                if ($playlistData[ApiResponse::POS_STATUS] !== ApiResponse::STATUS_SUCCESS) {
                    $this->sendResponse($playlistData);
                }
                $data = $this->getRequestBody();
                $trackId = $data[Constants::JSON_TRACK_ID] ?? null;
                $this->validateId($trackId);
                $response = $this->model->addTrack((int)$playlistId, (int)$trackId);
                $this->sendResponse($response);
            default:
                $this->sendErrorResponse('Invalid number of parameters', 400);
                exit;
        }
    }
    public function handleDelete()
    {
        switch (count($this->params)) {
            case 1:
                $playlistId = $this->params[0];
                $this->validateId($playlistId);
                $response = $this->model->delete((int)$playlistId);
                $this->sendResponse($response);
                break;
            case 3:
                $playlistId = $this->params[0];
                $this->validateId($playlistId);
                $trackId = $this->params[2];
                $this->validateId($trackId);
                $response = $this->model->removeTrack((int)$playlistId, (int)$trackId);
                $this->sendResponse($response);
            default:
                $this->sendErrorResponse('Invalid number of parameters', 400);
                exit;
        }

    }
}