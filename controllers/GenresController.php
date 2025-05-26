<?php
require_once 'BaseController.php';
require_once BASE_PATH . '/classes/Genres.php';

class GenresController extends BaseController
{
    public function initialize($urlData)
    {
        $this->model = new Genres();
        parent::initialize($urlData);
    }
    public function handleGet()
    {
        switch (count($this->params)) {
            case 0:
                $response = $this->model->list();
                $this->sendResponse($response);
                break;
            default:
                $this->sendErrorResponse('Invalid number of parameters', 400);
                exit;
            }
    }
}