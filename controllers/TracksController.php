<?php
require_once 'BaseController.php';
require_once BASE_PATH . '/classes/Tracks.php';

class TracksController extends BaseController
{
    public function initialize($urlData)
    {
        $this->model = new Tracks();
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
                if (!empty($this->query[Constants::QUERY_COMPOSER])) {
                    $composerQuery = $this->query[Constants::QUERY_COMPOSER];
                    $response = $this->model->searchByComposer($composerQuery);
                    $this->sendResponse($response);
                }
                break;
            case 1:
                $id = $this->params[0];
                $this->validateId($id);
                $response = $this->model->get((int)$id);
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
                $albumId = $data[Constants::JSON_ALBUM_ID] ?? null;
                $this->validateId($albumId);
                $mediaTypeId = $data[Constants::JSON_MEDIA_TYPE_ID] ?? null;
                $this->validateId($mediaTypeId);
                $genreId = $data[Constants::JSON_GENRE_ID] ?? null;
                $this->validateId($genreId);
                $composer = $data[Constants::JSON_COMPOSER] ?? null;
                $milliseconds = $data[Constants::JSON_MILLISECONDS] ?? null;
                $this->validateId($milliseconds);
                $bytes = $data[Constants::JSON_BYTES] ?? null;
                $this->validateId($bytes);
                $unitPrice = $data[Constants::JSON_UNIT_PRICE] ?? null;
                $this->validateId($unitPrice);
                if (
                    is_null($name) ||
                    is_null($albumId) ||
                    is_null($mediaTypeId) ||
                    is_null($genreId) ||
                    is_null($composer) ||
                    is_null($milliseconds) ||
                    is_null($bytes) ||
                    is_null($unitPrice)
                ) {
                    $this->sendErrorResponse('Missing required parameters', 400);
                }
                $response = $this->model->create(
                    $name,
                    (int)$albumId,
                    (int)$mediaTypeId,
                    (int)$genreId,
                    $composer,
                    (int)$milliseconds,
                    (int)$bytes,
                    (float)$unitPrice
                );
                $this->sendResponse($response);
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters', 400);
                exit;
        }
    }

    public function handlePut()
    {
        switch (count($this->params)) {
            case 1:
                $id = $this->params[0];
                $this->validateId($id);
                $trackData = $this->model->get((int)$id);
                if ($trackData[ApiResponse::POS_STATUS] !== ApiResponse::STATUS_SUCCESS) {
                    $this->sendErrorResponse('Track not found', 404);
                }
                $oldTrack = $trackData[ApiResponse::POS_DATA];
                $data = $this->getRequestBody();
                $name = $data[Constants::JSON_NAME] ?? $oldTrack[Constants::JSON_NAME];
                $albumId = $data[Constants::JSON_ALBUM_ID] ?? $oldTrack[Constants::JSON_ALBUM_ID];
                $this->validateId($albumId);
                $mediaTypeId = $data[Constants::JSON_MEDIA_TYPE_ID] ?? $oldTrack[Constants::JSON_MEDIA_TYPE_ID];
                $this->validateId($mediaTypeId);
                $genreId = $data[Constants::JSON_GENRE_ID] ?? $oldTrack[Constants::JSON_GENRE_ID];
                $this->validateId($genreId);
                $composer = $data[Constants::JSON_COMPOSER] ?? $oldTrack[Constants::JSON_COMPOSER];
                $milliseconds = $data[Constants::JSON_MILLISECONDS] ?? $oldTrack[Constants::JSON_MILLISECONDS];
                $this->validateId($milliseconds);
                $bytes = $data[Constants::JSON_BYTES] ?? $oldTrack[Constants::JSON_BYTES];
                $this->validateId($bytes);
                $unitPrice = $data[Constants::JSON_UNIT_PRICE] ?? $oldTrack[Constants::JSON_UNIT_PRICE];

                $response = $this->model->update(
                    (int)$id,
                    $name,
                    (int)$albumId,
                    (int)$mediaTypeId,
                    (int)$genreId,
                    $composer,
                    (int)$milliseconds,
                    (int)$bytes,
                    (float)$unitPrice
                );
                $this->sendResponse($response);
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters', 400);
        }
    }

}
