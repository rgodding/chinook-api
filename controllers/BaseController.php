<?php

abstract class BaseController 
{
    protected array $params;
    protected array $query;
    protected object $model;

    public function __construct(array $urlData) {   
        $this->params = $urlData[Constants::PARAMS] ?? [];
        $this->query = $_GET; // Capture query parameters if needed
        $this->initialize($urlData[Constants::METHOD] ?? Constants::METHOD_GET);
    }

    public function initialize($method){
        switch ($method) {
            case Constants::METHOD_GET:
                $this->handleGet();
                break;
            case Constants::METHOD_POST:
                $this->handlePost();
                break;
            case Constants::METHOD_PUT:
                $this->handlePut();
                break;
            case Constants::METHOD_PATCH:
                $this->handlePatch();
                break;
            case Constants::METHOD_DELETE:
                $this->handleDelete();
                break;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method not allowed']);
                exit;
        }
    }

    // Abstract methods to be implemented by subclasses
    protected function handleGet() {
        // Default implementation can be overridden by subclasses
        $this->sendErrorResponse('GET method not implemented', 501);
    }
    protected function handlePost() {
        // Default implementation can be overridden by subclasses
        $this->sendErrorResponse('POST method not implemented', 501);
    }
    protected function handlePut() {
        // Default implementation can be overridden by subclasses
        $this->sendErrorResponse('PUT method not implemented', 501);
    }
    protected function handlePatch() {
        // Default implementation can be overridden by subclasses
        $this->sendErrorResponse('PATCH method not implemented', 501);
    }
    protected function handleDelete() {
        // Default implementation can be overridden by subclasses
        $this->sendErrorResponse('DELETE method not implemented', 501);
    }


    // Helper function to send a response
    protected function sendResponse(array $data) {
        header('Content-Type: application/json');
        switch($data[ApiResponse::POS_STATUS]){
            case ApiResponse::STATUS_SUCCESS:
                http_response_code(200);
                $response = $data[ApiResponse::POS_DATA];
                echo json_encode($response);
                break;
            case ApiResponse::STATUS_SUCCESS_CREATED:
                http_response_code(201);
                $response = $data[ApiResponse::POS_DATA];
                echo json_encode($response);
                break;
            case ApiResponse::STATUS_SUCCESS_NO_CONTENT:
                http_response_code(204);
                // No content to return
                break;
            case ApiResponse::STATUS_SUCCESS_NOT_FOUND:
                http_response_code(404);
                $message = $data[ApiResponse::POS_MESSAGE] ?? 'Resource not found';
                echo json_encode(['error' => $message]);
                break;
            case ApiResponse::STATUS_ERROR_CONFLICT:
                http_response_code(409);
                $message = $data[ApiResponse::POS_MESSAGE] ?? 'Conflict occurred';
                echo json_encode(['error' => $message]);
                break;
            default:
            echo json_encode([
                'error' => [
                    'code' => $data[ApiResponse::POS_STATUS],
                    'message' => $data[ApiResponse::POS_MESSAGE] ?? 'An error occurred'
                ]]);
        }
        exit;
    }
    
    protected function sendErrorResponse(string $message, int $code = 400, array $details = []) {
        $error_log = [
            'message' => $message,
            'code' => $code,
            'details' => $details
        ];
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details
            ]
            ]);
    }

    // Helper Function
    protected function validateId($id): void {
        if($id <= 0) {
            $this->sendErrorResponse('Invalid ID format', 400, ['ID must be a numeric value greater than 0.']);
            exit;
        }
        if(!is_numeric($id)) {
            $this->sendErrorResponse('Invalid ID format', 400, ['ID must be a numeric value.']);
            exit;
        }
    }

    protected function getRequestBody(): array {
        $body = file_get_contents('php://input');
        if (empty($body)) {
            return [];
        }
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendErrorResponse('Invalid JSON format', 400, ['error' => json_last_error_msg()]);
            exit;
        }
        return $data;
    }

}