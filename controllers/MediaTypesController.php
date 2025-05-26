<?php
require_once 'BaseController.php';
require_once BASE_PATH . '/classes/MediaTypes.php';

class MediaTypesController extends BaseController
{
    public function initialize($urlData)
    {
        $this->model = new MediaTypes();
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