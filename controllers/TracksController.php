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
}